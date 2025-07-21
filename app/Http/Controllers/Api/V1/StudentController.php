<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $students = $request->user()->school->students()
            ->with(['class', 'class_arm', 'parent', 'session'])
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('admission_no', 'like', '%' . $request->search . '%');
            })
            ->when($request->has('class_id'), function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            })
            ->when($request->has('parent_id'), function ($query) use ($request) {
                $query->where('parent_id', $request->parent_id);
            })
            ->paginate(10);

        return response()->json($students);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'parent_id' => 'required|exists:parents,id',
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'required|exists:class_arms,id',
            'class_section_id' => 'nullable|exists:class_sections,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        $school = $request->user()->school;
        $session = \App\Models\Session::find($request->session_id);
        $admission_number = $session->name . '/' . ($school->students()->where('session_id', $session->id)->count() + 1);

        $student = $school->students()->create(array_merge($request->all(), [
            'id' => str()->uuid(),
            'admission_no' => $admission_number,
        ]));

        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        if ($student->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'parent_id' => 'required|exists:parents,id',
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'required|exists:class_arms,id',
            'class_section_id' => 'nullable|exists:class_sections,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        $student->update($request->all());

        return response()->json($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Student $student)
    {
        if ($student->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($student->results()->exists() || $student->attendances()->exists() || $student->fee_payments()->exists()) {
            return response()->json(['message' => 'Cannot delete student with dependent records.'], 409);
        }

        $student->delete();

        return response()->json(null, 204);
    }
}
