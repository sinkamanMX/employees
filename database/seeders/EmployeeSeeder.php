<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $educations = ['Bachelor', 'Master', 'PhD', 'High School', 'Associate'];
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Hyderabad', 'Pune', 'Kolkata', 'Ahmedabad'];
        $genders = ['Male', 'Female', 'Other'];
        
        for ($i = 0; $i < 1000; $i++) {
            Employee::create([
                'education' => $faker->randomElement($educations),
                'joining_year' => $faker->numberBetween(2015, 2024),
                'city' => $faker->randomElement($cities),
                'payment_tier' => $faker->numberBetween(1, 3),
                'age' => $faker->numberBetween(22, 60),
                'gender' => $faker->randomElement($genders),
                'ever_benched' => $faker->boolean(30), // 30% probabilidad
                'experience_in_current_domain' => $faker->numberBetween(0, 15),
                'leave_or_not' => $faker->boolean(20), // 20% probabilidad
            ]);
        }
    }
}
