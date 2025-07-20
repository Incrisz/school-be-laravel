<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SchoolParent;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $parents = SchoolParent::withCount('students')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            })
            ->paginate(10);

        return response()->json($parents);
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:parents,phone',
            'email' => 'nullable|email|unique:parents,email',
        ]);

        $parent = SchoolParent::create($request->all());

        return response()->json($parent, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolParent  $parent
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolParent $parent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolParent  $parent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolParent $parent)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:parents,phone,' . $parent->id,
            'email' => 'nullable|email|unique:parents,email,' . $parent->id,
        ]);

        $parent->update($request->all());

        return response()->json($parent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolParent  $parent
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolParent $parent)
    {
        if ($parent->students()->exists()) {
            return response()->json(['message' => 'Cannot delete parent with linked students.'], 409);
        }

        $parent->delete();

        return response()->json(null, 204);
    }
}
