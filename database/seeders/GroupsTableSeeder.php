<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupsTableSeeder extends Seeder
{
    public function run()
    {
        Group::create(['name' => 'ВТ-22', 'description' => 'Вычислительные технологии, 2 курс, 2 группа']);
        Group::create(['name' => 'ПО-31', 'description' => 'Программное обеспечение, 3 курс, 1 группа']);
    }
}
