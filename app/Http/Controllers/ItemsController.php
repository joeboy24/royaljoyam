<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Expense;
use App\Models\ItemAudit;
use App\Models\CompanyBranch;
use App\Models\ItemImage;
use App\Models\Category;
use App\Services\BranchTransferService;
use Exception;

class ItemsController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $filters = $this->resolveInventoryListFilters($request);
        $itemsQuery = $this->buildInventoryItemsQuery($filters);
        $match = ['del' => $filters['showRecycle'] ? 'yes' : 'no'];

        $filteredItemCount = (clone $itemsQuery)->count();
        $grandTotalCount = Item::where($match)->count();

        $items = $itemsQuery->orderBy('id', 'desc')->paginate($filters['perPage'])->appends(
            $this->inventoryListQueryParams(
                $filters['itemsearch'],
                $filters['showRecycle'],
                $filters['filterCategory'],
                $filters['filterStock'],
                $filters['perPage']
            )
        );

        $ITM = ItemImage::All();
        $cats = Category::All();
        $filterCategories = Item::where($match)
            ->whereNotNull('cat')
            ->where('cat', '!=', '')
            ->distinct()
            ->orderBy('cat')
            ->pluck('cat');

        $pass = [
            'c' => 1,
            'i' => 1,
            'ITM' => $ITM,
            'cats' => $cats,
            'items' => $items,
            'itemsearch' => $filters['itemsearch'],
            'filteredItemCount' => $filteredItemCount,
            'grandTotalCount' => $grandTotalCount,
            'showRecycle' => $filters['showRecycle'],
            'lowStockThreshold' => $filters['lowStockThreshold'],
            'filterCategory' => $filters['filterCategory'],
            'filterStock' => $filters['filterStock'],
            'perPage' => $filters['perPage'],
            'filterCategories' => $filterCategories,
            'inventoryListQuery' => $this->inventoryListQueryParams(
                $filters['itemsearch'],
                $filters['showRecycle'],
                $filters['filterCategory'],
                $filters['filterStock'],
                $filters['perPage']
            ),
        ];

        return view('pages.dash.itemsview')->with($pass);
    }

    public function exportInventory(Request $request)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $filters = $this->resolveInventoryListFilters($request);
        $items = $this->buildInventoryItemsQuery($filters)->orderBy('id', 'desc')->get();
        $branches = session('compbranch');
        $threshold = $filters['lowStockThreshold'];
        $filename = ($filters['showRecycle'] ? 'inventory-recycle-' : 'inventory-').date('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($items, $branches, $threshold) {
            $handle = fopen('php://output', 'w');
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

            fputcsv($handle, $headers);

            foreach ($items as $item) {
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

                for ($i = 0; $i < count($branches); $i++) {
                    $field = 'q'.($i + 1);
                    $row[] = $item->$field ?? 0;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function printInventory(Request $request)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $filters = $this->resolveInventoryListFilters($request);
        $items = $this->buildInventoryItemsQuery($filters)->orderBy('id', 'desc')->get();

        return view('pages.invoice.inventoryprint', [
            'items' => $items,
            'company' => Company::find(1),
            'filterSummary' => $this->inventoryFilterSummary($filters),
            'filteredItemCount' => $items->count(),
            'lowStockThreshold' => $filters['lowStockThreshold'],
            'showRecycle' => $filters['showRecycle'],
        ]);
    }

    private function resolveInventoryListFilters(Request $request): array
    {
        $showRecycle = $request->query('recycle') === '1';
        $filterStock = (string) $request->query('stock', '');

        if (!in_array($filterStock, ['low', 'has_branch'], true)) {
            $filterStock = '';
        }

        return [
            'showRecycle' => $showRecycle,
            'lowStockThreshold' => Item::LOW_STOCK_THRESHOLD,
            'itemsearch' => trim((string) $request->query('itemsearch', '')),
            'filterCategory' => trim((string) $request->query('category', '')),
            'filterStock' => $filterStock,
            'perPage' => $this->allowedInventoryPerPage((int) $request->query('per_page', 10)),
            'branchCount' => count(session('compbranch')),
        ];
    }

    private function buildInventoryItemsQuery(array $filters)
    {
        $match = ['del' => $filters['showRecycle'] ? 'yes' : 'no'];
        $itemsQuery = Item::where($match);

        if ($filters['itemsearch'] !== '') {
            $itemsQuery->where('name', 'like', '%'.$filters['itemsearch'].'%');
        }

        if ($filters['filterCategory'] !== '') {
            $itemsQuery->where('cat', $filters['filterCategory']);
        }

        if (!$filters['showRecycle'] && $filters['filterStock'] !== '') {
            $this->applyInventoryStockFilter(
                $itemsQuery,
                $filters['filterStock'],
                $filters['lowStockThreshold'],
                $filters['branchCount']
            );
        }

        return $itemsQuery;
    }

    private function inventoryFilterSummary(array $filters): string
    {
        $parts = [];

        if ($filters['showRecycle']) {
            $parts[] = 'Recycle bin';
        }

        if ($filters['itemsearch'] !== '') {
            $parts[] = 'Search: '.$filters['itemsearch'];
        }

        if ($filters['filterCategory'] !== '') {
            $parts[] = 'Category: '.$filters['filterCategory'];
        }

        if ($filters['filterStock'] === 'low') {
            $parts[] = 'Low stock only';
        } elseif ($filters['filterStock'] === 'has_branch') {
            $parts[] = 'Has branch stock';
        }

        return count($parts) > 0 ? implode(' · ', $parts) : 'All items';
    }

    private function allowedInventoryPerPage(int $perPage): int
    {
        return in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
    }

    private function applyInventoryStockFilter($query, string $filterStock, int $lowStockThreshold, int $branchCount): void
    {
        if ($filterStock === 'low') {
            $query->whereRaw('(qty + 0) <= ?', [$lowStockThreshold]);
            return;
        }

        if ($filterStock === 'has_branch' && $branchCount > 0) {
            $query->where(function ($subQuery) use ($branchCount) {
                for ($i = 1; $i <= $branchCount; $i++) {
                    $subQuery->orWhereRaw('(q' . $i . ' + 0) > 0');
                }
            });
        }
    }

    private function inventoryListQueryParams(
        string $itemsearch,
        bool $showRecycle,
        string $filterCategory,
        string $filterStock,
        int $perPage
    ): array {
        return array_filter([
            'itemsearch' => $itemsearch !== '' ? $itemsearch : null,
            'recycle' => $showRecycle ? '1' : null,
            'category' => $filterCategory !== '' ? $filterCategory : null,
            'stock' => $filterStock !== '' ? $filterStock : null,
            'per_page' => $perPage !== 10 ? $perPage : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = new Company;
        $user = new User;
        $cat = new Category;
        $item = new Item;
        //
        try {

            switch ($request->input('store_action')) {

                case 'create_user':

                    // $user = new User;
                    $ps1 = $request->input('password');
                    $ps2 = $request->input('password_confirmation');
                    $status = $request->input('status');

                    // $uc = CompanyBranch::where('name', $status)->first();
                    // $uc = CompanyBranch::where('name', $status)->first();
                    if($status == 'Administrator'){
                        $bv = 'A';
                        $br = 1;
                        $br = 1;
                    }else{
                        $uc = CompanyBranch::find($status);
                        $uc = CompanyBranch::find($status);
                        $bv = $uc->tag;
                        $br = $status;
                        $status = $uc->name;
                        $br = $status;
                        $status = $uc->name;
                    }
    
    
                    try {
                        if($ps1 == $ps2){
                            $user->name = $request->input('name');
                            $user->email = $request->input('email');
                            $user->password = Hash::make($ps1);
                            $user->company_branch_id = $br;
                            $user->company_branch_id = $br;
                            $user->bv = $bv;
                            $user->status = $status;
                            $user->save();


                            return redirect('/dashuser')->with('success', 'User Created Successfully');
                        }else{
                            return redirect('/dashuser')->with('error', 'Passwords do not match');
                        }
                        
                            // // Update branch user id... Assign to this user
                            // $this_id = User::Latest('id')->first();
                            // $companybranch_id = CompanyBranch::where('name', $request->input('name'))->get('id');
                            // $bfind = CompanyBranch::find($companybranch_id);
                            // $bfind->user_id = $this_id;
                            // $bfind->save();

                    } catch (\Throwable $th) {
                        // throw $th;
                        return redirect('/dashuser')->with('error', 'Ooops... Username / Email already exists '.$th);
                    }
    
                break;

                case 'add_cat':

                    $cat->user_id = auth()->user()->id;
                    $cat->name = $request->input('name');
                    $cat->desc = $request->input('desc');
                    $cat->save();
                    return redirect('/dashuser')->with('success', 'Category Created Successfully');

                break;

                case 'add_order':

                    $ref = $request->input('ref');

                    try {
                        // $this->validate($request, [
                        //     'repfile'   => 'required|max:5000|mimes:jpeg,jpg,png'
                        // ]);
                        if($request->hasFile('repfile')){
                            //get filename with ext
                            $filenameWithExt = $request->file('repfile')->getClientOriginalName();
                            //get filename
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            //get file ext
                            $fileExt = $request->file('repfile')->getClientOriginalExtension();
                            //filename to store
                            $filenameToStore = 'rjv.'.$fileExt;
                            //upload path
                            $path = $request->file('repfile')->storeAs('public/rjv_receipts', $filenameToStore);
                            // return redirect('/order')->with('success', 'Successfull 3');

                        }else{
                            $filenameToStore = 'noimage.jpg';
                        }
            
                    } catch (Exception $ex) {
                        return redirect('/order')->with('error', 'Ooops..! Unhandled Error -->');
                    }



                    try {

                        $order = new Order;
                        $order->ref = $ref;
                        $order->user_id = auth()->user()->id;
                        $order->company_name = $request->input('company_name');
                        $order->contact = $request->input('contact');
                        $order->desc = $request->input('desc');
                        $order->tot = $request->input('tot');
                        $order->order_receipt = $filenameToStore;
                        
                        $order->save();

                        return redirect('/orders')->with('success', 'Order '.$ref.' successfully added');

                    }catch(Exception $ex) {
                        $ex2 = $ex->getMessage();
                        $ex2 = substr($ex2,0,100).'.....!';
                        return redirect('/orders')->with('error', 'Ooops..! Records already exists.');
                       
                    }
    
                break;

                case 'add_item':

                    $returnTo = $request->input('return_to') === 'items' ? '/items' : '/dashuser';
                    $it_no = 'MT'.date('dis');
                    $name = $request->input('name');
                    $barcode = $request->input('barcode');
                    $matchThese = ['name' => $name, 'del' => 'no'];

                    $results = Item::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect($returnTo)->with('error', 'Oops..! Item already exist');
                    }else{
                        $Id = null;

                        try {
                            
                            $im = new ItemImage;
                            $im->item_id = $it_no;
                            $im->save();

                            $Id = $im->id;
                            //return redirect('/dashuser')->with('success', $a);

                            $filenameToStore = 'no_image.png';

                            if($request->hasFile('items')){

                                // $this->validate($request, [
                                //     'items' => 'required|max:5000|mimes:jpg,jpeg,png'
                                // ]);

                                $c = 1;
                                $hold = '';
                                foreach($request->file('items') as $file){
                                    $filenameWithExt = $file->getClientOriginalName();
                                    //get filename
                                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                    //get file ext
                                    $fileExt = $file->getClientOriginalExtension();
                                    //filename to store
                                    $tmpFile = 'item'.date('i-s').$c.'.';
                                    $filenameToStore = $tmpFile.$fileExt;
                                    //upload path
                                    $path = $file->storeAs('public/rjv_items', $filenameToStore);

                                    //$im_search = ItemImage::where('item_no', $it_no)->get();
                                    $im2 = ItemImage::find($Id);
                                    $h = 'img'.$c;
                                    $im2->$h = $filenameToStore;
                                    $im2->save();

                                    $c++;
                                }
                                
                            }
                

                        } catch (\Throwable $th) {
                            if ($Id) {
                                ItemImage::find($Id)?->delete();
                            }
                            return redirect($returnTo)->with('error', 'Ooops..! Unhandled Error ');
                        }

                        $full = ItemImage::find($Id);

                        try {
                            $cp = $request->input('price');
                            // $sp = $request->input('price');
                            // $profits = $cp - $sp;

                            $item->user_id = auth()->user()->id;
                            $item->itemimage_id = $full->id;
                            $item->item_no = $it_no;
                            $item->name = $name;
                            $item->desc = $request->input('desc');
    
                            $item->cat = $request->input('cat');
                            $item->brand = $request->input('brand');
                            $item->barcode = $barcode;

                            $item->qty = $request->input('qty');
                            $item->cost_price = $cp;
                            $item->price = $cp;
                            // $item->profits = $profits;
                            $item->img = $filenameToStore;
                            $item->thumb_img = $filenameToStore;
    
                            $item->save();

                            $search2 = Item::where('item_no', $it_no)->first();
                            $id2 = $search2->id;

                            // Change item_id from M... to $id in ItemImage table
                            $im3 = ItemImage::find($Id);
                            $im3->item_id = $id2;
                            $im3->save();
                            

                            return redirect($returnTo)->with('success', 'Item successfully added');
                        } catch(\Throwable $th){
                            return redirect($returnTo)->with('error', 'Oops..! Unhandled Error! ');
                        }
                        
                    }

                break;

                case 'update_item':

                    return redirect('/items')->with('error', 'Item update should be handled through the stock update form');

                break;

                case 'admi_config':

                    $name = $request->input('name');
                    $loc = $request->input('loc');
                    // $matchThese = ['name' => $name, 'location' => $loc, 'del' => 'no'];

                    $results = Company::find(1);

                    if ($results){
                        try {
                            $company = Company::find(1);
                            $company->user_id = auth()->user()->id;
                            $company->name = $name;
                            session('company')->address = $request->input('company_add');
    
                            $company->location = $loc;
                            session('company')->contact = $request->input('contact');
    
                            session('company')->email = $request->input('email');
                            $company->website = $request->input('company_web');
                            $company->reg_date = Date('d-m-Y');
                            $company->logo = $request->input('company_logo');
    
                            $company->save();
                            return redirect('/config')->with('success', 'Company`s details successfully updated');
                        } catch(Exception $ex){
                            return redirect('/config')->with('error', 'Oops..! Unhandled error');
                        }

                    }else{

                        try {
                            $this->validate($request, [
                                'company_logo'   => 'required|max:5000|mimes:jpeg,jpg,png'
                            ]);
                            if($request->hasFile('company_logo')){
                                //get filename with ext
                                $filenameWithExt = $request->file('company_logo')->getClientOriginalName();
                                //get filename
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                //get file ext
                                $fileExt = $request->file('company_logo')->getClientOriginalExtension();
                                //filename to store
                                $filenameToStore = 'company_logo.'.$fileExt;
                                //upload path
                                $path = $request->file('company_logo')->storeAs('public/ss_imgs', $filenameToStore);
                            }else{
                                $filenameToStore = '';
                            }
                
                        } catch (Exception $ex) {
                            return redirect('/config')->with('error', 'Ooops..! Unhandled Error');
                        }
                        

                        try {
                            $company->user_id = auth()->user()->id;
                            $company->name = $name;
                            session('company')->address = $request->input('company_add');
    
                            $company->location = $loc;
                            session('company')->contact = $request->input('contact');
    
                            session('company')->email = $request->input('email');
                            $company->website = $request->input('company_web');
                            $company->reg_date = Date('d-m-Y');
                            $company->logo = $filenameToStore;
    
                            $company->save();
                            return redirect('/config')->with('success', 'Company`s details successfully added');
                        } catch(Exception $ex){
                            return redirect('/config')->with('error', 'Ooops..! Unhandled Error');
                        }
                        
                    }
                    
                break;

                case 'create_branch':

                    try {
                        //code...
                        $bc = CompanyBranch::all()->count();
                        $branch = new CompanyBranch;
                        $branch->user_id = auth()->user()->id;
                        $branch->name = $request->input('name');
                        $branch->loc = $request->input('loc');
                        $branch->contact = $request->input('contact');
                        // $branch->tag = 'b'.($bc + 1);
                        $branch->tag = $bc + 1;
                        $branch->save();
                        return redirect('/config')->with('success', 'Branch Created Successfully');
                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect('/config')->with('error', 'Oops..! '.$request->input('name').' already created under company branches.');
                    }
    
                break;

                case 'update_branch':

                    $my_id = $request->input('id');
                    $item_up = Item::find($my_id);
                    $item_up->b1 = $request->input('b1');
                    $item_up->b2 = $request->input('b2');
                    $item_up->b3 = $request->input('b3');
                    $item_up->save();
                    return redirect('/dashuser')->with('success', 'Branch prices for "'.$request->input('comp_name').'" has been updated');
                   
                    //Hash::make($data['password']);
    
                break;

                case 'update_branch_qty':

                    $my_id = $request->input('id2');
                    $item_up = Item::find($my_id);

                    $q1 = $request->input('x1');
                    $q2 = $request->input('y2');
                    $q3 = $request->input('z3');
                    $qs = $q1 + $q2 + $q3;

                    if($qs == $item_up->qty){
                        $item_up->q1 = $q1;
                        $item_up->q2 = $q2;
                        $item_up->q3 = $q3;
                        $item_up->save();
                        return redirect('/dashuser')->with('success', 'Branch quantities for "'.$request->input('comp_name2').'" has been updated');
                    }else{
                        return redirect('/dashuser')->with('error', 'Oops...! Sum of branch quantities cannot be greater or less than the QUANTITY AVAILABLE');
                    }
                    // return $my_id;
    
                break;

            }
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Oops..! Something went wrong while processing that request. '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $item = Item::find($id);
        $qty = $item->qty;

        if ($qty < 1){
            $qty = "Out of Stock";
        }else{
            $qty = "Available";
        }
        //$itemImgs = ItemImage::find($id);

        $pass = [
            'item' => $item,
            'qty' => $qty
        ];
        return view('pages.products_det')->with($pass);

        // $item = Item::find($id);
        // $item2 = $item->itemimage->img1;
        // return $item2;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (auth()->user()->status != 'Administrator') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item = Item::with('user')->find($id);

        if (!$item || $item->del === 'yes') {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $branches = session('compbranch', collect());
        $branchPayload = [];

        for ($i = 0; $i < count($branches); $i++) {
            $qField = 'q' . ($i + 1);
            $bField = 'b' . ($i + 1);

            $branchPayload[] = [
                'index' => $i + 1,
                'tag' => (string) $branches[$i]->tag,
                'name' => $branches[$i]->name,
                'qty' => (int) ($item->$qField ?? 0),
                'price' => number_format((float) ($item->$bField ?? 0), 2, '.', ''),
            ];
        }

        return response()->json([
            'id' => $item->id,
            'item_no' => $item->item_no,
            'name' => $item->name,
            'desc' => $item->desc,
            'cat' => $item->cat,
            'brand' => $item->brand,
            'barcode' => $item->barcode,
            'qty' => (int) $item->qty,
            'price' => number_format((float) $item->price, 2, '.', ''),
            'thumb_img' => $item->thumb_img ?: 'no_image.png',
            'creator_name' => $item->user->name ?? 'Unknown',
            'branches' => $branchPayload,
            'update_url' => action('ItemsController@update', $item->id),
            'transfer_url' => action('ItemsController@transferStock', $item->id),
        ]);
    }

    public function transferStock(Request $request, $id)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $validated = $request->validate([
            'from_branch' => 'required|string',
            'to_branch' => 'required|string|different:from_branch',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::find($id);

        if (! $item || $item->del === 'yes') {
            return redirect('/items')->with('error', 'Item not found.');
        }

        try {
            $service = app(BranchTransferService::class);
            $transfer = $service->transfer(
                $item,
                $validated['from_branch'],
                $validated['to_branch'],
                (int) $validated['qty'],
                auth()->user(),
                $validated['notes'] ?? null
            );
        } catch (\InvalidArgumentException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        $fromName = $service->branchName($transfer->from_branch);
        $toName = $service->branchName($transfer->to_branch);

        return redirect()->back()->with(
            'success',
            'Transferred '.number_format((int) $transfer->qty).' units of '.$item->name.' from '.$fromName.' to '.$toName.'.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        switch ($request->input('store_action')) {

            case 'update_item':

                $item = Item::find($id);

                if (!$item) {
                    return redirect('/items')->with('error', 'Item not found');
                }

                try {
                    $generalQty = max(0, (int) $request->input('qty', 0));
                    $branchQtyTotal = 0;
                    $branchCount = count(session('compbranch'));

                    $basePriceInput = $request->input('price');
                    if (!is_numeric($basePriceInput) || (float) $basePriceInput < 0) {
                        return redirect('/items')->with('error', 'Enter a valid base price (0 or greater).');
                    }
                    $basePrice = number_format((float) $basePriceInput, 2, '.', '');

                    for ($i = 1; $i <= $branchCount; $i++) {
                        $qq = 'q' . $i;
                        $bb = 'b' . $i;
                        $qtyValue = max(0, (int) $request->input($qq, 0));
                        $branchPriceInput = $request->input($bb, 0);

                        if (!is_numeric($branchPriceInput) || (float) $branchPriceInput < 0) {
                            return redirect('/items')->with('error', 'Enter valid branch prices (0 or greater).');
                        }

                        $branchQtyTotal += $qtyValue;
                        $item->$qq = $qtyValue;
                        $item->$bb = number_format((float) $branchPriceInput, 2, '.', '');
                    }

                    if ($branchQtyTotal > $generalQty) {
                        return redirect('/items')->with('error', 'Sum of branch quantities cannot be greater than general quantity.');
                    }

                    $item->name = trim($request->input('name', ''));
                    $item->desc = $request->input('desc');
                    $item->cat = $request->input('cat');
                    $item->brand = $request->input('brand');
                    $item->barcode = $request->input('barcode');
                    $item->qty = $generalQty;
                    $item->price = $basePrice;
                    $item->cost_price = $basePrice;
                    $item->save();

                    return redirect('/items')->with('success', 'Record successfully updated');
                } catch (Exception $ex) {
                    return redirect('/items')->with('error', 'Oops..! Unhandled Error! ');
                }

            break;

            case 'del_item':

                $item = Item::find($id);
                if (!$item) {
                    return redirect('/items')->with('error', 'Item not found');
                }

                try {
                    $item->del = 'yes';
                    $item->save();
                    return redirect('/items')->with('success', 'Item successfully deleted');
                } catch(Exception $ex){
                    return redirect('/items')->with('error', 'Oops..! Unhandled Error!');
                }      

                    
            break;

            case 'restore_item':

                $item = Item::find($id);
                if (!$item) {
                    return redirect('/items?recycle=1')->with('error', 'Item not found');
                }

                try {
                    $item->del = 'no';
                    $item->save();
                    return redirect('/items?recycle=1')->with('success', 'Item successfully restored');
                } catch(Exception $ex){
                    return redirect('/items?recycle=1')->with('error', 'Oops..! Unhandled Error!');
                }

            break;

            case 'print_invoice':

                return view('pages.dash.invoice');  
                    
            break;

        }

        return redirect('/items')->with('error', 'Unknown update action.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        switch ($request->input('del_action')) {

            case 'cat_del':
                $cat = Category::find($id);

                if (! $cat) {
                    return redirect(url()->previous())->with('error', 'Category not found.');
                }

                if ($cat->isInUse()) {
                    return redirect(url()->previous())->with('error', 'Cannot delete category — it is assigned to one or more inventory items.');
                }

                $cat->delete();

                return redirect(url()->previous())->with('success', 'Category Deleted.');
            break;

            case 'item_del':
                $item = Item::find($id);
                $item->del = 'yes';
                $item->save();
                return redirect(url()->previous())->with('success', 'Course Deleted');
            break;

            case 'usr_del':
                $user = User::find($id);
                $user->del = 'yes';
                $user->save();
                return redirect(url()->previous())->with('success', 'User Deleted.');
            break;

            case 'usr_restore':
                $user = User::find($id);
                $user->del = 'no';
                $user->save();
                return redirect(url()->previous())->with('success', 'User Successfully Restored.');
            break;

            case 'branch_del':
                $branch = CompanyBranch::find($id);
                $branch->del = 'yes';
                $branch->save();
                return redirect(url()->previous())->with('success', 'Branch Deleted.');
            break;
        }
    }
}
