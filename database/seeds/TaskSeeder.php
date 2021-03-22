<?php

use Illuminate\Database\Seeder;

use App\Task;
use App\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::truncate();
        $faker = \Faker\Factory::create();

        $users = User::all();

        foreach ($users as $u ) {
            for ( $i=0; $i < 5; $i++ ) {
                Task::create([
                    'name' => $faker->name,
                    'description' => $faker->text(100),
                    'type' => $faker->randomElement(Task::TYPES),
                    'status' => $faker->randomElement(Task::STATUSES),
                    'owner_id' => $u->id,
                ]);
            }
        }
    }
}
