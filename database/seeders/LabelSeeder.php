<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labels = ['work', 'home', 'main', 'mobile', 'no label', 'work fax', 'home fax', 'pager', 'other'];

        foreach ($labels as $label) {
            DB::table('labels')->insert([
                'label' => Str::title($label)
            ]);
        }
    }
}
