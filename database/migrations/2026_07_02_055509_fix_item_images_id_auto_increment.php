<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables imported without AUTO_INCREMENT on primary key id columns.
     *
     * If `php artisan migrate` fails with "Field 'id' doesn't have a default value"
     * on the migrations table, run database/scripts/fix_auto_increment_keys.sql first.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'item_images',
        'items',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $this->ensureAutoIncrementPrimaryKey($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::statement(
                "ALTER TABLE {$table} MODIFY id BIGINT UNSIGNED NOT NULL"
            );
        }
    }

    private function ensureAutoIncrementPrimaryKey(string $table): void
    {
        $createSql = DB::select("SHOW CREATE TABLE {$table}")[0]->{'Create Table'} ?? '';

        if (str_contains($createSql, 'AUTO_INCREMENT')) {
            return;
        }

        $maxId = (int) DB::table($table)->max('id');
        $nextId = max($maxId + 1, 1);

        DB::statement(
            "ALTER TABLE {$table} MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT"
        );
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = {$nextId}");
    }
};
