<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class InventoryCsvService
{
    public const MAX_IMPORT_ROWS = 500;

    /**
     * Active inventory is empty when no non-deleted items exist.
     */
    public function isActiveInventoryEmpty(): bool
    {
        return ! Item::where('del', 'no')->exists();
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function activeCategoryNames(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->values();
    }

    public function hasCategories(): bool
    {
        return $this->activeCategoryNames()->isNotEmpty();
    }

    /**
     * Headers for an upload-ready CSV/Excel template (aligned with add-item fields).
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string>
     */
    public function templateHeaders(Collection $branches): array
    {
        $headers = [
            'Name',
            'Description',
            'Category',
            'Brand',
            'Barcode',
            'General Qty',
            'Base Price (Gh)',
        ];

        foreach ($branches as $branch) {
            $headers[] = $branch->name.' Qty';
        }

        return $headers;
    }

    /**
     * Headers for the current inventory data export.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string>
     */
    public function dataExportHeaders(Collection $branches): array
    {
        $headers = [
            'Item No',
            'Name',
            'Category',
            'Brand',
            'Barcode',
            'General Qty',
            'Stock Status',
            'Base Price (Gh)',
            'Date',
        ];

        foreach ($branches as $branch) {
            $headers[] = $branch->name.' Qty';
        }

        return $headers;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string|int|float>
     */
    public function dataExportRow(Item $item, Collection $branches, int $threshold): array
    {
        $row = [
            $item->item_no,
            $item->name,
            $item->cat,
            $item->brand,
            $item->barcode,
            $item->qty,
            $item->stockBadgeLabel($threshold),
            number_format((float) $item->price, 2, '.', ''),
            $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '',
        ];

        for ($i = 0; $i < $branches->count(); $i++) {
            $field = 'q'.($i + 1);
            $row[] = $item->$field ?? 0;
        }

        return $row;
    }

    /**
     * Build an Excel upload template with a Category dropdown locked to registered categories.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @param  \Illuminate\Support\Collection<int, string>  $categories
     */
    public function buildUploadTemplateSpreadsheet(Collection $branches, Collection $categories): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventory');

        $headers = $this->templateHeaders($branches);
        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $defaultCategory = (string) $categories->first();
        // Prefill Category on the first data row so the dropdown starts with a valid value.
        $sheet->setCellValue('C2', $defaultCategory);

        $categorySheet = new Worksheet($spreadsheet, 'Categories');
        $spreadsheet->addSheet($categorySheet);
        foreach ($categories->values() as $i => $name) {
            $categorySheet->setCellValue('A'.($i + 1), $name);
        }
        $categorySheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $lastCategoryRow = max(1, $categories->count());
        $categoryRange = 'Categories!$A$1:$A$'.$lastCategoryRow;
        $maxRow = self::MAX_IMPORT_ROWS + 1;

        // One validation rule for the whole Category column (faster / safer on shared hosts).
        $validation = $sheet->getCell('C2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Invalid category');
        $validation->setError('Choose a category from the list. Free text is not allowed.');
        $validation->setPromptTitle('Category');
        $validation->setPrompt('Select a registered category.');
        $validation->setFormula1($categoryRange);
        $validation->setSqref('C2:C'.$maxRow);

        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->getFont()->setBold(true);
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Write an XLSX upload template to a real temp file.
     *
     * ZipArchive (used by PhpSpreadsheet) cannot reliably write to php://output
     * on many shared hosts, which produces 0-byte downloads.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @param  \Illuminate\Support\Collection<int, string>  $categories
     */
    public function writeUploadTemplateXlsxToTemp(Collection $branches, Collection $categories): string
    {
        $tempBase = tempnam(sys_get_temp_dir(), 'invtpl');
        if ($tempBase === false) {
            throw new \RuntimeException('Could not create a temporary file for the Excel template.');
        }

        $path = $tempBase.'.xlsx';
        @unlink($tempBase);

        $spreadsheet = $this->buildUploadTemplateSpreadsheet($branches, $categories);

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($path);
        } finally {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        if (! is_file($path) || filesize($path) === 0) {
            @unlink($path);
            throw new \RuntimeException('Excel template was not generated. Ensure the PHP zip extension is enabled.');
        }

        return $path;
    }

    /**
     * @deprecated Prefer writeUploadTemplateXlsxToTemp() for downloads.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @param  \Illuminate\Support\Collection<int, string>  $categories
     * @param  resource|string  $outputStream
     */
    public function writeUploadTemplateXlsx(Collection $branches, Collection $categories, $outputStream): void
    {
        if (is_string($outputStream) && $outputStream !== 'php://output' && $outputStream !== 'php://stdout') {
            $spreadsheet = $this->buildUploadTemplateSpreadsheet($branches, $categories);
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputStream);
            $spreadsheet->disconnectWorksheets();

            return;
        }

        $path = $this->writeUploadTemplateXlsxToTemp($branches, $categories);

        try {
            readfile($path);
        } finally {
            @unlink($path);
        }
    }

    /**
     * Import inventory rows from an uploaded CSV or Excel file.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return array{created: int, skipped: int, errors: list<string>}
     */
    public function import(UploadedFile $file, Collection $branches, int|string $userId): array
    {
        $rows = $this->readImportRows($file);
        if ($rows === null) {
            return [
                'created' => 0,
                'skipped' => 0,
                'errors' => ['Could not read the uploaded file. Use the Excel template or a CSV export.'],
            ];
        }

        if ($rows === []) {
            return [
                'created' => 0,
                'skipped' => 0,
                'errors' => ['File is empty or missing a header row.'],
            ];
        }

        $headerRow = array_shift($rows);
        $columnMap = $this->mapHeaders($headerRow, $branches);
        if (! isset($columnMap['name'])) {
            return [
                'created' => 0,
                'skipped' => 0,
                'errors' => ['File must include a Name column. Download the template when inventory is empty.'],
            ];
        }

        $allowedCategories = $this->activeCategoryNames()
            ->mapWithKeys(fn ($name) => [mb_strtolower($name) => $name])
            ->all();

        $created = 0;
        $skipped = 0;
        $errors = [];
        $seenNames = [];
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;

            if ($this->rowIsBlank($row)) {
                continue;
            }

            if (($created + $skipped) >= self::MAX_IMPORT_ROWS) {
                $errors[] = 'Stopped after '.self::MAX_IMPORT_ROWS.' data rows (maximum per upload).';
                break;
            }

            $parsed = $this->parseRow($row, $columnMap, $branches, $rowNumber, $seenNames, $allowedCategories);
            if ($parsed['error'] !== null) {
                $skipped++;
                if (count($errors) < 15) {
                    $errors[] = $parsed['error'];
                }
                continue;
            }

            if ($parsed['data'] === null) {
                continue;
            }

            try {
                DB::transaction(function () use ($parsed, $userId, $branches) {
                    $this->createItemFromRow($parsed['data'], $userId, $branches);
                });
                $created++;
                $seenNames[mb_strtolower($parsed['data']['name'])] = true;
            } catch (Throwable $e) {
                $skipped++;
                if (count($errors) < 15) {
                    $errors[] = 'Row '.$rowNumber.': could not save item.';
                }
            }
        }

        return compact('created', 'skipped', 'errors');
    }

    /**
     * @return list<list<string|null>>|null
     */
    private function readImportRows(UploadedFile $file): ?array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');
        $path = $file->getRealPath();

        if ($path === false) {
            return null;
        }

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            try {
                $spreadsheet = IOFactory::load($path);
                $sheet = $spreadsheet->getSheetByName('Inventory') ?? $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);
                $spreadsheet->disconnectWorksheets();

                return array_map(function ($row) {
                    return array_map(fn ($cell) => $cell === null ? '' : (string) $cell, $row);
                }, $rows);
            } catch (Throwable $e) {
                return null;
            }
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return null;
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @param  list<string|null>  $headerRow
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return array<string, int>
     */
    public function mapHeaders(array $headerRow, Collection $branches): array
    {
        $map = [];
        $branchLookup = [];

        foreach ($branches as $index => $branch) {
            $key = $this->normalizeKey($branch->name.' Qty');
            $branchLookup[$key] = $index;
            $branchLookup[$this->normalizeKey($branch->name)] = $index;
        }

        foreach ($headerRow as $colIndex => $rawHeader) {
            $normalized = $this->normalizeKey((string) $rawHeader);

            if ($normalized === '') {
                continue;
            }

            $field = $this->canonicalField($normalized);
            if ($field !== null) {
                $map[$field] = $colIndex;
                continue;
            }

            if (isset($branchLookup[$normalized])) {
                $map['branch_'.$branchLookup[$normalized]] = $colIndex;
                continue;
            }

            if (str_ends_with($normalized, ' qty')) {
                $branchName = trim(substr($normalized, 0, -4));
                if (isset($branchLookup[$branchName])) {
                    $map['branch_'.$branchLookup[$branchName]] = $colIndex;
                }
            }
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnMap
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @param  array<string, bool>  $seenNames
     * @param  array<string, string>  $allowedCategories lowercase => canonical name
     * @return array{error: ?string, data: ?array<string, mixed>}
     */
    private function parseRow(
        array $row,
        array $columnMap,
        Collection $branches,
        int $rowNumber,
        array $seenNames,
        array $allowedCategories
    ): array {
        $name = $this->cell($row, $columnMap, 'name');
        $desc = $this->cell($row, $columnMap, 'description');
        $cat = $this->cell($row, $columnMap, 'category');
        $brand = $this->cell($row, $columnMap, 'brand');
        $barcode = $this->cell($row, $columnMap, 'barcode');
        $qtyRaw = $this->cell($row, $columnMap, 'general_qty');
        $priceRaw = $this->cell($row, $columnMap, 'base_price');

        // Template pre-fills Category on row 2; skip until the user adds item details.
        if ($name === '' && $desc === '' && $qtyRaw === '' && $priceRaw === '') {
            return ['error' => null, 'data' => null];
        }

        if ($name === '') {
            return ['error' => 'Row '.$rowNumber.': Name is required.', 'data' => null];
        }

        if ($desc === '') {
            return ['error' => 'Row '.$rowNumber.': Description is required.', 'data' => null];
        }

        if ($cat === '') {
            return ['error' => 'Row '.$rowNumber.': Category is required.', 'data' => null];
        }

        $catKey = mb_strtolower($cat);
        if (! isset($allowedCategories[$catKey])) {
            return ['error' => 'Row '.$rowNumber.': category "'.$cat.'" is not registered. Add it in Registry first.', 'data' => null];
        }
        $cat = $allowedCategories[$catKey];

        if ($qtyRaw === '' || ! is_numeric($qtyRaw) || (float) $qtyRaw < 0 || floor((float) $qtyRaw) != (float) $qtyRaw) {
            return ['error' => 'Row '.$rowNumber.': General Qty must be a whole number of 0 or more.', 'data' => null];
        }

        if ($priceRaw === '' || ! is_numeric($priceRaw) || (float) $priceRaw < 0) {
            return ['error' => 'Row '.$rowNumber.': Base Price must be a number of 0 or more.', 'data' => null];
        }

        $nameKey = mb_strtolower($name);
        if (isset($seenNames[$nameKey])) {
            return ['error' => 'Row '.$rowNumber.': duplicate Name "'.$name.'" in this file.', 'data' => null];
        }

        if (Item::where(['name' => $name, 'del' => 'no'])->exists()) {
            return ['error' => 'Row '.$rowNumber.': item "'.$name.'" already exists.', 'data' => null];
        }

        $qty = (int) $qtyRaw;
        $price = number_format((float) $priceRaw, 2, '.', '');
        $branchQtys = [];
        $branchTotal = 0;

        for ($i = 0; $i < $branches->count(); $i++) {
            $raw = $this->cell($row, $columnMap, 'branch_'.$i);
            if ($raw === '') {
                $branchQtys[$i] = 0;
                continue;
            }

            if (! is_numeric($raw) || (float) $raw < 0 || floor((float) $raw) != (float) $raw) {
                return ['error' => 'Row '.$rowNumber.': branch quantity must be a whole number of 0 or more.', 'data' => null];
            }

            $branchQtys[$i] = (int) $raw;
            $branchTotal += $branchQtys[$i];
        }

        if ($branchTotal > $qty) {
            return [
                'error' => 'Row '.$rowNumber.': branch quantities ('.$branchTotal.') exceed General Qty ('.$qty.').',
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'data' => [
                'name' => $name,
                'desc' => $desc,
                'cat' => $cat,
                'brand' => $brand,
                'barcode' => $barcode,
                'qty' => $qty,
                'price' => $price,
                'branch_qtys' => $branchQtys,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     */
    private function createItemFromRow(array $data, int|string $userId, Collection $branches): void
    {
        $image = new ItemImage;
        $image->item_id = 'pending';
        $image->save();

        $itemNo = 'MT'.date('ymdHis').str_pad((string) ($image->id % 10000), 4, '0', STR_PAD_LEFT);

        $item = new Item;
        $item->user_id = (string) $userId;
        $item->itemimage_id = (string) $image->id;
        $item->item_no = $itemNo;
        $item->name = $data['name'];
        $item->desc = $data['desc'];
        $item->cat = $data['cat'];
        $item->brand = $data['brand'] !== '' ? $data['brand'] : null;
        $item->barcode = $data['barcode'] !== '' ? $data['barcode'] : null;
        $item->qty = (string) $data['qty'];
        $item->cost_price = $data['price'];
        $item->price = $data['price'];
        $item->img = 'no_image.png';
        $item->thumb_img = 'no_image.png';

        for ($i = 0; $i < $branches->count(); $i++) {
            $field = 'q'.($i + 1);
            $priceField = 'b'.($i + 1);
            $item->$field = (string) ($data['branch_qtys'][$i] ?? 0);
            $item->$priceField = $data['price'];
        }

        $item->save();

        $image->item_id = (string) $item->id;
        $image->save();
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnMap
     */
    private function cell(array $row, array $columnMap, string $field): string
    {
        if (! isset($columnMap[$field])) {
            return '';
        }

        $value = $row[$columnMap[$field]] ?? '';

        return trim((string) $value);
    }

    /**
     * @param  list<string|null>  $row
     */
    private function rowIsBlank(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeKey(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower(trim($header));
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return $header;
    }

    private function canonicalField(string $normalized): ?string
    {
        return match ($normalized) {
            'name', 'item name' => 'name',
            'description', 'desc' => 'description',
            'category', 'cat' => 'category',
            'brand' => 'brand',
            'barcode' => 'barcode',
            'general qty', 'qty', 'quantity' => 'general_qty',
            'base price (gh)', 'base price', 'price', 'cost price', 'cost price (gh)', 'cost price (gh₵)' => 'base_price',
            // Ignore export-only columns.
            'item no', 'stock status', 'date' => null,
            default => null,
        };
    }
}
