<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SkillCategory;
use App\Models\SkillType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SkillCategoryController extends Controller
{
    public function index(Request $request)
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json([
                'message' => 'Authenticated user is not associated with any school.',
            ], 422);
        }

        $categories = SkillCategory::query()
            ->where('school_id', $school->id)
            ->with(['skill_types' => function ($query) {
                $query->orderBy('name')
                    ->select(['id', 'skill_category_id', 'name', 'description', 'weight']);
            }])
            ->orderBy('name')
            ->get()
            ->map(function (SkillCategory $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'skill_types' => $category->skill_types->map(function (SkillType $type) {
                        return [
                            'id' => $type->id,
                            'name' => $type->name,
                            'description' => $type->description,
                            'weight' => $type->weight,
                            'skill_category_id' => $type->skill_category_id,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        $school = $request->user()->school;

        if (! $school) {
            return response()->json([
                'message' => 'Authenticated user is not associated with any school.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('skill_categories', 'name')->where('school_id', $school->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = SkillCategory::create([
            'id' => (string) Str::uuid(),
            'school_id' => $school->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'message' => 'Skill category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, SkillCategory $skillCategory)
    {
        $this->authorizeCategory($request, $skillCategory);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('skill_categories', 'name')->ignore($skillCategory->id)->where('school_id', $skillCategory->school_id)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        $skillCategory->fill($validated);

        if ($skillCategory->isDirty()) {
            $skillCategory->save();
        }

        return response()->json([
            'message' => 'Skill category updated successfully.',
            'data' => $skillCategory->fresh(),
        ]);
    }

    public function destroy(Request $request, SkillCategory $skillCategory)
    {
        $this->authorizeCategory($request, $skillCategory);

        $skillCategory->delete();

        return response()->json([
            'message' => 'Skill category deleted successfully.',
        ]);
    }

    private function authorizeCategory(Request $request, SkillCategory $skillCategory): void
    {
        $user = $request->user();

        if (! $user || $user->school_id !== $skillCategory->school_id) {
            abort(403, 'You are not allowed to manage this skill category.');
        }
    }
}
