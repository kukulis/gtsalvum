<?php

use Illuminate\Database\Seeder;

use App\Message;
use App\User;
use App\Task;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Message::truncate();
        $faker = \Faker\Factory::create();

        $users = User::all();
        $tasks = Task::all();

        // each user 1 message for each task
        foreach ($users as $user ) {
            foreach ($tasks as $task) {
                Message::create(
                    [
                        'subject'  => $faker->name,
                        'message'  => $faker->text(100),
                        'owner_id' => $user->id,
                        'task_id'  => $task->id,
                    ]
                );
            }
        }
    }
}
