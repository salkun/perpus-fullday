<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitLog;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VisitLog::with('visitor')->latest('visited_at');

        // Filter by date (default: hari ini)
        if ($request->date) {
            $query->whereDate('visited_at', $request->date);
        } else if ($request->all !== 'true') {
            $query->whereDate('visited_at', today());
        }

        if ($request->visitor_id) {
            $query->where('visitor_id', $request->visitor_id);
        }

        $logs = $query->paginate($request->per_page ?? 20);

        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'purpose'    => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'visited_at' => 'nullable|date',
        ]);

        // Default visited_at = sekarang
        $validated['visited_at'] = $validated['visited_at'] ?? now();

        $log = VisitLog::create($validated);
        $log->load('visitor');

        return response()->json($log, 201);
    }

    public function update(Request $request, VisitLog $visitLog)
    {
        $validated = $request->validate([
            'purpose' => 'nullable|string|max:255',
            'notes'   => 'nullable|string',
            'left_at' => 'nullable|date|after:visited_at',
        ]);

        $visitLog->update($validated);

        return response()->json($visitLog->load('visitor'));
    }

    public function destroy(VisitLog $visitLog)
    {
        $visitLog->delete();

        return response()->json(['message' => 'Log kunjungan berhasil dihapus.']);
    }

    public function summary()
    {
        return response()->json([
            'today'       => VisitLog::whereDate('visited_at', today())->count(),
            'this_month'  => VisitLog::whereMonth('visited_at', now()->month)
                                     ->whereYear('visited_at', now()->year)
                                     ->count(),
            'total'       => VisitLog::count(),
            'total_books' => \App\Models\Book::count(),
            'total_visitors' => Visitor::count(),
        ]);
    }
}
