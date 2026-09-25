<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            [
                'task_name' => 'Finish Laravel mini project',
                'description' => 'Complete the Personal Task Manager CRUD app.',
                'status' => 'Pending',
                'due_date' => now()->addDays(3),
            ],
            [
                'task_name' => 'Review Blade templating notes',
                'description' => 'Go over layouts, sections, and components.',
                'status' => 'Completed',
                'due_date' => now()->subDay(),
            ],
            [
                'task_name' => 'Prepare project README',
                'description' => 'Add screenshots and setup instructions.',
                'status' => 'Pending',
                'due_date' => now()->addDays(5),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}