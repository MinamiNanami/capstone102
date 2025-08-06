<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Asia/Manila'); // optional timezone
        $month = $now->month;
        $year = $now->year;

        $schedules = Schedule::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        return view('schedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Schedule::create([
            'title' => $request->title,
            'date' => $request->date,
            'time' => $request->time,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Schedule added successfully!');
    }
}
