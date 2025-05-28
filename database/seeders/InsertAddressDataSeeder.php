<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InsertAddressDataSeeder extends Seeder
{
    public function run()
    {
        $sqlPath = database_path('seeders/sql/');

        if (!File::exists($sqlPath)) {
            $this->command->error("Directory not found: {$sqlPath}");
            return;
        }

        $files = [
            'province.sql',
            'amphur.sql',
            'district.sql',
            'zipcode.sql',
            'rule.sql',
            'position.sql',
            'init_data.sql',
        ];

        foreach ($files as $file) {
            $filePath = $sqlPath . $file;
            if (File::exists($filePath)) {
                $sql = File::get($filePath);
                DB::unprepared($sql);
                $this->command->info("Seeded: {$file}");
            } else {
                $this->command->error("File not found: {$file}");
            }
        }
    }
}
