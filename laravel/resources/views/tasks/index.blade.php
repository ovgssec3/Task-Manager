@extends('layouts.app')

@section('title', 'All Tasks - Personal Task Manager')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="stats">
        <div class="stat">
            <div class="num">{{ $tasks->count() }}</div>
            <div class="label">Showing</div>
        </div>
        <div class="stat">
            <div class="num">{{ $pendingCount }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat">
            <div class="num">{{ $completedCount }}</div>
            <div class="label">Completed</div>
        </div>
    </div>

    <div class="card">
        <div class="toolbar">
            <div class="filters">
                <a href="{{ route('tasks.index') }}" class="{{ request('status') ? '' : 'active' }}">All</a>
                <a href="{{ route('tasks.index', ['status' => 'Pending']) }}" class="{{ request('status') === 'Pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('tasks.index', ['status' => 'Completed']) }}" class="{{ request('status') === 'Completed' ? 'active' : '' }}">Completed</a>
            </div>

            <form method="GET" action="{{ route('tasks.index') }}" style="display:flex; gap:8px;">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}" style="min-width:180px;">
                <button class="btn btn-outline btn-sm" type="submit">Search</button>
            </form>

            <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ Add Task</a>
        </div>

        @if ($tasks->isEmpty())
            <div class="empty">
                <p>No tasks found.</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Add your first task</a>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td data-label="Task"><strong>{{ $task->task_name }}</strong></td>
                            <td data-label="Description" class="desc-cell">{{ $task->description ?: '—' }}</td>
                            <td data-label="Due Date">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                                @if ($task->isOverdue())
                                    <div><span class="badge badge-overdue">Overdue</span></div>
                                @endif
                            </td>
                            <td data-label="Status">
                                <span class="badge {{ $task->status === 'Completed' ? 'badge-completed' : 'badge-pending' }}">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <form class="inline" method="POST" action="{{ route('tasks.status', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $task->status === 'Completed' ? 'Pending' : 'Completed' }}">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            {{ $task->status === 'Completed' ? 'Mark Pending' : 'Mark Done' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form class="inline" method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection