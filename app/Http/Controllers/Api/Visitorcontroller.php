<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::withCount('visitLogs')
                        ->with('latestVisit');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('member_id', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        $visitors = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($visitors);
    }

    public function show(Visitor $visitor)
    {
        $visitor->load('visitLogs');
        $visitor->loadCount('visitLogs');

        return response()->json($visitor);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'  => 'nullable|string|max:20|unique:visitors,member_id',
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'occupation' => 'nullable|string|max:100',
        ]);

        $visitor = Visitor::create($validated);

        return response()->json($visitor, 201);
    }

    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'member_id'  => 'nullable|string|max:20|unique:visitors,member_id,' . $visitor->id,
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'occupation' => 'nullable|string|max:100',
        ]);

        $visitor->update($validated);

        return response()->json($visitor);
    }

    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return response()->json(['message' => 'Data pengunjung berhasil dihapus.']);
    }
}
