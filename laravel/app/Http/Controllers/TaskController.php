<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::query();

        if ($request->filled('status') && in_array($request->status, ['Pending', 'Completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('task_name', 'like', '%'.$request->search.'%');
        }

        $tasks = $query->orderBy('due_date')->orderByDesc('created_at')->get();

        $pendingCount = Task::pending()->count();
        $completedCount = Task::completed()->count();

        return view('tasks.index', compact('tasks', 'pendingCount', 'completedCount'));
    }

    public function create(): View
    {
        return view('tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pending,Completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task added successfully.');
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pending,Completed'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Completed'],
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->route('tasks.index')->with('success', 'Task status updated.');
    }
}