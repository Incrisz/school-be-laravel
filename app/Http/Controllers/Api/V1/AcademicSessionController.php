<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicSessionController extends Controller
{
    // Session Management
    public function index()
    {
        return response()->json(Session::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sessions,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $existingSession = Session::where(function ($query) use ($validated) {
            $query->where('start_date', '<=', $validated['end_date'])
                  ->where('end_date', '>=', $validated['start_date']);
        })->exists();

        if ($existingSession) {
            return response()->json(['error' => 'A session already exists within the specified date range.'], 400);
        }

        $session = Session::create($validated);
        return response()->json(['message' => 'Session created successfully', 'data' => $session], 201);
    }

    public function show(Session $session)
    {
        return response()->json($session);
    }

    public function update(Request $request, Session $session)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:sessions,name,' . $session->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $existingSession = Session::where('id', '!=', $session->id)
            ->where(function ($query) use ($validated) {
                $query->where('start_date', '<=', $validated['end_date'])
                      ->where('end_date', '>=', $validated['start_date']);
            })->exists();

        if ($existingSession) {
            return response()->json(['error' => 'A session already exists within the specified date range.'], 400);
        }

        $session->update($validated);
        return response()->json(['message' => 'Session updated successfully', 'data' => $session]);
    }

    public function destroy(Session $session)
    {
        if ($session->terms()->exists()) {
            return response()->json(['error' => 'Cannot delete session with linked terms.'], 400);
        }

        $session->delete();
        return response()->json(['message' => 'Session deleted successfully']);
    }

    // Term Management
    public function getTermsForSession(Session $session)
    {
        return response()->json($session->terms);
    }

    public function storeTerm(Request $request, Session $session)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $existingTerm = $session->terms()->where(function ($query) use ($validated) {
            $query->where('start_date', '<=', $validated['end_date'])
                  ->where('end_date', '>=', $validated['start_date']);
        })->exists();

        if ($existingTerm) {
            return response()->json(['error' => 'A term already exists within the specified date range for this session.'], 400);
        }

        $term = $session->terms()->create($validated);
        return response()->json(['message' => 'Term created successfully', 'data' => $term], 201);
    }

    public function updateTerm(Request $request, Term $term)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $existingTerm = $term->session->terms()->where('id', '!=', $term->id)
            ->where(function ($query) use ($validated) {
                $query->where('start_date', '<=', $validated['end_date'])
                      ->where('end_date', '>=', $validated['start_date']);
            })->exists();

        if ($existingTerm) {
            return response()->json(['error' => 'A term already exists within the specified date range for this session.'], 400);
        }

        $term->update($validated);
        return response()->json(['message' => 'Term updated successfully', 'data' => $term]);
    }

    public function destroyTerm(Term $term)
    {
        // Add logic to check for linked students or results
        // if ($term->students()->exists() || $term->results()->exists()) {
        //     return response()->json(['error' => 'Cannot delete term with linked students or results.'], 400);
        // }

        $term->delete();
        return response()->json(['message' => 'Term deleted successfully']);
    }
}
