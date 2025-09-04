<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('date') && !empty($request->date)) {
            $schedules = Schedule::whereDate('date', $request->date)->get();
        } else {
            $now = Carbon::now('Asia/Manila');
            $schedules = Schedule::whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->get();
        }

        $events = $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title ?? 'Appointment',
                'start' => $schedule->date . ' ' . $schedule->time,
                'description' => $schedule->description ?? '',
            ];
        });

        return view('schedule', [
            'schedules' => $schedules,
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Schedule::where('date', $request->date)
                        ->where('time', $value)
                        ->exists();
                    if ($exists) {
                        $fail('An event already exists at this date and time.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'phone_number' => 'required|string', // Add phone number field for SMS
            'customer_name' => 'required|string|max:255', // Add customer name for SMS
        ]);

        try {
            $schedule = Schedule::create($request->only('title', 'customer_name', 'date', 'time', 'description', 'phone_number'));

            // Send SMS after creating the schedule
            $message = "Hi {$request->customer_name}, your appointment is scheduled on {$request->date} at {$request->time}.";
            $this->sendSms($request->phone_number, $message);

            return redirect()->back()->with('success', 'Schedule added successfully and SMS sent!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong while saving the schedule.');
        }
    }

    // Helper function to send SMS
    private function sendSms($phoneNumber, $message)
    {
        $response = Http::asForm()->post('https://sms.iprogtech.com/api/v1/sms_messages', [
            'api_token' => env('IPROG_SMS_API_TOKEN'),
            'message' => $message,
            'phone_number' => $phoneNumber,
        ]);

        return $response->successful();
    }
}