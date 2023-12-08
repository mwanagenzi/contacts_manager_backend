<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group_names = ['default', 'first default', 'second default'];

        foreach ($group_names as $group_name) {
            $group = Group::create([
                'name' => $group_name,
            ]);
            ContactGroup::create([
                'group_id' => $group->id,
                'contact_id' => fake()->randomDigitNotZero()
            ]);
        }
    }
}
