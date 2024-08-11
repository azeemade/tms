<?php

namespace Tests\Unit\Http\Controllers\Task;

use App\Http\Controllers\Task\TaskController;
use App\Http\Requests\Task\TaskFormRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $taskController;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskController = new TaskController();
        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    public function test_index_returns_user_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create(); // Other user's tasks

        $response = $this->taskController->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(3, $response->getData()->data);
    }

    public function test_show_returns_task_for_authorized_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->taskController->show($task->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($task->id, $response->getData()->data->id);
    }

    public function test_show_returns_not_found_for_nonexistent_task()
    {
        $response = $this->taskController->show(999);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_adds_new_task()
    {
        $request = new TaskFormRequest([
            'title' => 'Test Task',
            'description' => 'Test Description'
        ]);

        $response = $this->taskController->create($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Test Task', $response->getData()->data->title);
        $this->assertEquals($this->user->id, $response->getData()->data->user_id);
    }

    public function test_update_modifies_task_for_authorized_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        $request = new TaskFormRequest([
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ]);

        $response = $this->taskController->update($request, $task->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Updated Title', $response->getData()->data->title);
    }

    public function test_update_returns_not_found_for_nonexistent_task()
    {
        $request = new TaskFormRequest([
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ]);

        $response = $this->taskController->update($request, 999);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_destroy_deletes_task_for_authorized_user()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->taskController->destroy($task->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_returns_not_found_for_nonexistent_task()
    {
        $response = $this->taskController->destroy(999);

        $this->assertEquals(404, $response->getStatusCode());
    }
}