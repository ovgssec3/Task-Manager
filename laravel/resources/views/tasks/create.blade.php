@extends('layouts.app')

@section('title', 'Add Task - Personal Task Manager')

@section('content')
    <div class="card" style="max-width:560px; margin:0 auto;">
        <h2 style="margin-top:0;">Add New Task</h2>

        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf

            <div class="field">
                <label for="task_name">Task Name</label>
                <input type="text" id="task_name" name="task_name" value="{{ old('task_name') }}" placeholder="e.g. Finish project proposal" required>
                @error('task_name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Add any extra details...">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
                @error('due_date') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Pending" {{ old('status', 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">Save Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection