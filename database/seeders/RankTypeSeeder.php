<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RankType;
use Illuminate\Support\Facades\DB;

class RankTypeSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RankType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $types = ['Army', 'Navy', 'Air Force'];
        foreach ($types as $type) {
            RankType::create(['name' => $type]);
        }
    }
}
