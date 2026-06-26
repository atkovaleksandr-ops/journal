<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Group;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        $group = Group::first();
        Student::create(['group_id'=>$group->id,'first_name'=>'Ivan','last_name'=>'Ivanov','student_number'=>'S001']);
        Student::create(['group_id'=>$group->id,'first_name'=>'Maria','last_name'=>'Petrova','student_number'=>'S002']);
    }
}
