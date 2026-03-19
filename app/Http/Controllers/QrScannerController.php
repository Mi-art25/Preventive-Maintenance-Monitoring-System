<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class QrScannerController extends Controller
{
    public function index()
    {
        return view('scanner');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['category', 'location', 'maintenanceSchedules.technician.user',
            'maintenanceLogs' => fn($q) => $q->latest()->limit(5)]);

        $nextSchedule = $equipment->maintenanceSchedules()
            ->where('status', 'active')
            ->orderBy('next_due_date')
            ->first();

        $overdue = $equipment->maintenanceSchedules()->overdue()->count();

        return view('equipment.show', compact('equipment', 'nextSchedule', 'overdue'));
    }

    public function lookup(Request $request)
    {
        $query = $request->input('query', '');

        if (str_contains($query, '/equipment/')) {
            $id        = last(explode('/', rtrim($query, '/')));
            $equipment = Equipment::find($id);
        } else {
            $equipment = Equipment::where('serial_number', $query)->first();
        }

        if (!$equipment) {
            return response()->json(['found' => false, 'message' => 'Equipment not found.'], 404);
        }

        $equipment->load(['category', 'location']);

        $nextSchedule = $equipment->maintenanceSchedules()
            ->where('status', 'active')
            ->orderBy('next_due_date')
            ->first();

        $overdueCount = $equipment->maintenanceSchedules()->overdue()->count();

        return response()->json([
            'found'     => true,
            'equipment' => [
                'id'            => $equipment->id,
                'serial_number' => $equipment->serial_number,
                'name'          => $equipment->name,
                'brand'         => $equipment->brand,
                'model'         => $equipment->model,
                'status'        => $equipment->status,
                'category'      => $equipment->category?->name,
                'location'      => $equipment->location?->name,
                'qr_code_url'   => $equipment->qr_code_url,
                'detail_url'    => route('equipment.show', $equipment),
                'log_url'       => route('filament.admin.resources.maintenance-logs.create')
                                   . '?equipment_id=' . $equipment->id,
            ],
            'next_schedule' => $nextSchedule ? [
                'title'         => $nextSchedule->title,
                'next_due_date' => $nextSchedule->next_due_date->format('M d, Y'),
                'frequency'     => $nextSchedule->frequency,
                'is_overdue'    => $nextSchedule->is_overdue,
            ] : null,
            'overdue_count' => $overdueCount,
        ]);
    }
}