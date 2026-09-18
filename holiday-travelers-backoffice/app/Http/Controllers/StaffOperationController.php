<?php

namespace App\Http\Controllers;

use App\Models\StaffCommission;
use App\Models\StaffPerformance;
use App\Models\StaffSchedule;
use App\Models\StaffTask;
use App\Models\User;
use Illuminate\Http\Request;

class StaffOperationController extends Controller
{
    private array $types = [
        'tasks' => [StaffTask::class, 'Tasks', 'tasks', 'assigned_to'],
        'scheduling' => [StaffSchedule::class, 'Scheduling', 'scheduling', 'user_id'],
        'performance' => [StaffPerformance::class, 'Performance', 'performance', 'user_id'],
    ];

    public function index(Request $request)
    {
        [$model, $label, $slug] = $this->type($request);
        $records = $model::with($slug === 'tasks' ? 'assignee' : 'user')->latest()->paginate(15);
        $users = User::whereIn('role', ['admin', 'manager', 'agent', 'staff'])->orderBy('name')->get();
        return view('staff-operations.index', compact('records', 'users', 'label', 'slug'));
    }

    public function store(Request $request)
    {
        [$model, $label, $slug] = $this->type($request);
        $data = $this->validated($request, $slug);
        $model::create($data);
        return back()->with('success', $label . ' record added.');
    }

    public function update(Request $request, $record)
    {
        [$model, $label, $slug] = $this->type($request);
        $entry = $model::findOrFail($record);
        $data = $this->validated($request, $slug, true);
        $entry->update($data);
        return back()->with('success', $label . ' record updated.');
    }

    public function destroy(Request $request, $record)
    {
        [$model, $label] = $this->type($request);
        $model::findOrFail($record)->delete();
        return back()->with('success', $label . ' record removed.');
    }

    private function type(Request $request): array
    {
        $slug = $request->segment(1);
        abort_unless(isset($this->types[$slug]), 404);
        return [$this->types[$slug][0], $this->types[$slug][1], $slug, $this->types[$slug][3]];
    }

    private function validated(Request $request, string $slug, bool $update = false): array
    {
        $rules = match ($slug) {
            'tasks' => ['assigned_to' => 'required|exists:users,id', 'title' => 'required|string|max:255', 'description' => 'nullable|string', 'due_date' => 'nullable|date', 'priority' => 'required|in:low,normal,high', 'status' => 'required|in:todo,in_progress,done'],
            'scheduling' => ['user_id' => 'required|exists:users,id', 'schedule_date' => 'required|date', 'starts_at' => 'nullable|date_format:H:i', 'ends_at' => 'nullable|date_format:H:i', 'location' => 'nullable|string|max:255', 'notes' => 'nullable|string'],
            'performance' => ['user_id' => 'required|exists:users,id', 'period' => 'required|string|max:40', 'score' => 'required|numeric|min:0|max:100', 'bookings_completed' => 'required|integer|min:0', 'revenue_generated' => 'required|numeric|min:0', 'notes' => 'nullable|string'],
        };
        return $request->validate($rules);
    }
}
