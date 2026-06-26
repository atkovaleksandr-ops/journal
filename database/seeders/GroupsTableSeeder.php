<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupsTableSeeder extends Seeder
{
    public function run()
    {
        Group::create(['name' => 'Group A', 'description' => 'First group']);
        Group::create(['name' => 'Group B', 'description' => 'Second group']);
    }
}
