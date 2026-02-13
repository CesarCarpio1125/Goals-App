<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CalendarExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CalendarController extends Controller
{
    public function __construct(
        private CalendarExportService $calendarExportService
    ) {}

    public function index(Request $request): InertiaResponse
    {
        $goals = $request->user()
            ->goals()
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Calendar', [
            'goals' => $goals,
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $goals = $request->user()
            ->goals()
            ->with('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        $month = $request->query('month');
        $year = $request->query('year');

        $pdf = $this->calendarExportService->generateCalendarPdf(
            $request->user(),
            $goals,
            $month,
            $year
        );

        $filename = 'calendar-' . ($month ?: 'current') . '-' . ($year ?: date('Y')) . '.pdf';

        return $pdf->download($filename);
    }
}
