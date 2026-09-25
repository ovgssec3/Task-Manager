@extends('layouts.app')

@section('title', 'Edit Task - Personal Task Manager')

@section('content')
    <div class="card" style="max-width:560px; margin:0 auto;">
        <h2 style="margin-top:0;">Edit Task</h2>

        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="task_name">Task Name</label>
                <input type="text" id="task_name" name="task_name" value="{{ old('task_name', $task->task_name) }}" required>
                @error('task_name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                @error('due_date') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Pending" {{ old('status', $task->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Completed" {{ old('status', $task->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">Update Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection