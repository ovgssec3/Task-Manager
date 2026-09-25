# Personal Task Manager

**Project Code:** WST21-PM-2026-SF
**Student Name:** _[Your Name Here]_
**Course & Year:** _[Your Course & Year Here]_
**Database Used:** SQLite (default) — also works with MySQL / PostgreSQL by changing `.env`

A simple Laravel CRUD web app for managing personal tasks, built by following the classroom flow:
**Routes → Controller → Model → Database → Blade**

## Features

- Add Task
- View Tasks
- Edit Task
- Delete Task
- Update Status

### Extra features I added on top of the requirements
- Filter tasks by status (All / Pending / Completed)
- Search tasks by name
- Dashboard counters (total shown / pending / completed)
- Automatic "Overdue" badge for pending tasks whose due date has passed
- One-click "Mark Done / Mark Pending" status toggle (no separate edit page needed)
- Server-side form validation with inline error messages
- Responsive layout (table collapses into cards on mobile)
- Database seeder that inserts sample tasks for quick demoing

---

## How I Built This

I followed the order suggested in the assignment: **Database → Model → Controller → Routes → Blade → CRUD.** Below is a walkthrough of each step and *why* it was done that way.

### 1. Project setup

I started with a fresh Laravel installation:

```bash
composer create-project laravel/laravel task-manager
cd task-manager
```

Then configured the database connection in `.env`. I used **SQLite** because it needs zero setup — no separate database server to install or configure, just a single file:

```env
DB_CONNECTION=sqlite
```

```bash
touch database/database.sqlite
```

I initially considered MySQL (since that's what we used in our IM subject), but SQLite made local development and grading much simpler for a small project like this. The app still fully supports MySQL — you just swap the `.env` connection block and create the database.

### 2. Database — the migration

Everything starts from the data. I generated a migration for the `tasks` table:

```bash
php artisan make:migration create_tasks_table
```

Inside the migration, I defined exactly the fields the assignment required, plus timestamps (which Laravel adds automatically and are useful for sorting/auditing):

```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('task_name');
    $table->text('description')->nullable();
    $table->enum('status', ['Pending', 'Completed'])->default('Pending');
    $table->date('due_date')->nullable();
    $table->timestamps();
});
```

**Design decisions:**
- `status` is an `enum` restricted to `Pending`/`Completed` at the database level, so invalid statuses can't be saved even if validation is bypassed.
- `description` and `due_date` are `nullable` since not every task needs a deadline or extra notes.
- `status` defaults to `Pending` so a new task doesn't need the field explicitly set.

Then I ran the migration to actually create the table:

```bash
php artisan migrate
```

### 3. Model — `Task.php`

```bash
php artisan make:model Task
```

The `Task` model maps to the `tasks` table automatically (Laravel pluralizes the class name by convention). Key parts:

- **`$fillable`** — whitelists which fields can be mass-assigned via `Task::create($data)`. This is a security measure against mass-assignment vulnerabilities.
- **`$casts`** — casts `due_date` to a Carbon date object automatically, so I can call things like `$task->due_date->format('M d, Y')` or `$task->due_date->isPast()` directly in Blade without manual parsing.
- **Query scopes** (`scopePending`, `scopeCompleted`) — let me write clean, readable queries like `Task::pending()->count()` instead of repeating `where('status', 'Pending')` everywhere.
- **`isOverdue()`** — a small helper method that checks if a task is still `Pending` *and* its due date has already passed. This powers the "Overdue" badge in the UI.

### 4. Controller — `TaskController.php`

```bash
php artisan make:controller TaskController
```

I used a standard **resource controller** structure (`index`, `create`, `store`, `edit`, `update`, `destroy`) since it maps directly onto the required features:

| Method | Feature it implements |
|---|---|
| `index()` | View Tasks |
| `create()` + `store()` | Add Task |
| `edit()` + `update()` | Edit Task |
| `destroy()` | Delete Task |
| `updateStatus()` *(custom, extra method)* | Update Status |

I split status updates into their **own method** (`updateStatus`) rather than forcing users to open the full edit form just to flip a task between Pending/Completed. This is called from a one-click button on the task list.

Validation is done directly in the controller using `$request->validate([...])`, since the project is small enough that a dedicated Form Request class would be overkill:

```php
$request->validate([
    'task_name' => ['required', 'string', 'max:255'],
    'description' => ['nullable', 'string'],
    'status' => ['required', 'in:Pending,Completed'],
    'due_date' => ['nullable', 'date'],
]);
```

`index()` also accepts optional `status` and `search` query parameters, which power the filter buttons and search box on the task list.

### 5. Routes — `routes/web.php`

Instead of writing six separate `Route::get`/`Route::post` lines by hand, I used **route resource binding**, which generates all the standard CRUD routes in one line and automatically names them (`tasks.index`, `tasks.store`, etc.) — those names are used throughout the Blade views (`route('tasks.edit', $task)`):

```php
Route::resource('tasks', TaskController::class);
```

I added one extra route on top for the status toggle, since that's not part of the standard resource set:

```php
Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
    ->name('tasks.status');
```

I also redirected the root URL straight to `/tasks`, so the app opens directly on the task list instead of Laravel's default welcome page:

```php
Route::redirect('/', '/tasks');
```

**Route model binding:** Because the routes use `{task}` and the controller methods type-hint `Task $task`, Laravel automatically fetches the right `Task` record from the database (or throws a 404 if it doesn't exist) — no manual `Task::findOrFail($id)` needed.

### 6. Blade Views

I built a shared layout (`layouts/app.blade.php`) containing the `<head>`, all the CSS, and a header bar, with a single `@yield('content')` slot. Every page extends this layout with `@extends('layouts.app')` so the styling and structure stay consistent without duplicating HTML.

- **`tasks/index.blade.php`** — lists all tasks in a table, with status badges, filter links, a search form, and inline forms for the status-toggle/edit/delete actions.
- **`tasks/create.blade.php`** — the "Add Task" form.
- **`tasks/edit.blade.php`** — the same form pre-filled with the task's existing data via `old('field', $task->field)`, so validation errors don't wipe out what the user already typed.

Things I made sure to include in every form:
- `@csrf` — Laravel requires a CSRF token on every POST/PUT/PATCH/DELETE form, or the request is rejected.
- `@method('PUT')` / `@method('DELETE')` — HTML forms only support GET/POST natively, so Laravel "spoofs" the other HTTP verbs using a hidden `_method` field.
- `@error('field')` blocks — show validation error messages directly under each input.
- `old('field')` — repopulates the form with the previously submitted value if validation fails.

No CSS framework (like Bootstrap or Tailwind) was used — I wrote the styling by hand in `layouts/app.blade.php` using CSS variables for the color palette, so the whole design is easy to re-theme from one place.

### 7. Testing the CRUD flow

Once everything was wired up, I tested manually in this order:
1. Visit `/tasks` — confirms routes + `index()` + Blade rendering work.
2. Add a task — confirms `store()`, validation, and the redirect-with-flash-message pattern.
3. Edit a task — confirms route model binding and pre-filled forms.
4. Toggle status — confirms the dedicated `PATCH` route works independently of full edit.
5. Delete a task — confirms the `DELETE` form + confirmation dialog.
6. Filter/search — confirms the query-string based filtering in `index()`.

### 8. Deployment / running environment

I developed this inside a **GitHub Codespace**. One issue I ran into worth documenting: Laravel's `route()`/`url()` helpers normally build links from the incoming request's `Host` header. Inside Codespaces, the PHP dev server sees the request as `localhost:8000` even though the browser is actually going through the public forwarded domain — so every generated link was being built with `localhost:8000` baked in, which then broke once Codespaces' proxy tried to rewrite it.

The fix was forcing Laravel to always use `APP_URL` instead of trusting the Host header, in `AppServiceProvider::boot()`:

```php
public function boot(): void
{
    if (config('app.url')) {
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');
    }
}
```

This is a good example of an environment-specific quirk that doesn't show up when running Laravel locally on your own machine with plain `localhost`, only inside proxied dev environments like Codespaces.

---

## Tech Stack

- Laravel 11 (PHP 8.2+)
- Blade templating engine
- Eloquent ORM
- SQLite (default) — MySQL/PostgreSQL supported via `.env`

## Project Structure (key files)

```
app/Models/Task.php                              → Task model, scopes, isOverdue()
app/Http/Controllers/TaskController.php          → CRUD + status update logic
app/Providers/AppServiceProvider.php             → forces correct URL generation
database/migrations/..._create_tasks_table.php   → tasks table schema
database/factories/TaskFactory.php               → fake data generator
database/seeders/DatabaseSeeder.php              → sample task data
routes/web.php                                   → all task routes
resources/views/layouts/app.blade.php            → shared layout + all CSS
resources/views/tasks/index.blade.php            → task list / dashboard
resources/views/tasks/create.blade.php           → add task form
resources/views/tasks/edit.blade.php             → edit task form
```

### Database schema (`tasks` table)

| Field | Type | Notes |
|---|---|---|
| id | bigint, auto-increment | Primary key |
| task_name | string | Required |
| description | text, nullable | Optional details |
| status | enum('Pending','Completed') | Defaults to `Pending` |
| due_date | date, nullable | Optional deadline |
| created_at / updated_at | timestamps | Managed automatically by Laravel |

## Routes Overview

| Method | URI | Action | Name |
|---|---|---|---|
| GET | /tasks | List all tasks | tasks.index |
| GET | /tasks/create | Show add-task form | tasks.create |
| POST | /tasks | Store a new task | tasks.store |
| GET | /tasks/{task}/edit | Show edit-task form | tasks.edit |
| PUT | /tasks/{task} | Update a task | tasks.update |
| DELETE | /tasks/{task} | Delete a task | tasks.destroy |
| PATCH | /tasks/{task}/status | Update only the status | tasks.status |

---

## Setup Instructions

### 1. Clone the repository
```bash
git clone <your-repo-url>
cd task-manager
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Set up the environment file
```bash
cp .env.example .env
php artisan key:generate
```

Set `APP_URL` in `.env` to match wherever you're running the app (e.g. `http://localhost:8000`, or your Codespace's forwarded domain).

### 4. Configure the database

**Option A — SQLite (default, no server needed):**
```bash
touch database/database.sqlite
```

**Option B — MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=
```
```sql
CREATE DATABASE task_manager;
```

### 5. Run migrations (and optional sample data)
```bash
php artisan migrate
php artisan db:seed   # optional: adds 3 sample tasks
```

### 6. Start the development server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Visit **http://127.0.0.1:8000** — it redirects straight to the task list.

---

## Notes

- `vendor/` and `.env` are intentionally excluded from the repository (standard Laravel practice) — run `composer install` and copy `.env.example` after cloning.
- Built as part of the Laravel Mini Project assignment to practice the Routes → Controller → Model → Database → Blade flow.