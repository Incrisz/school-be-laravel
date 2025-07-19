<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Class;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @OA\Info(
 *      version="1.2",
 *      title="Class & Arm Setup",
 *      description="API for managing classes, class arms, and class sections"
 * )
 */
class ClassController extends Controller
{
    /**
     * @OA\Get(
     *      path="/classes",
     *      operationId="getClassesList",
     *      tags={"Classes"},
     *      summary="Get list of classes",
     *      description="Returns list of classes",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     *     )
     */
    public function index()
    {
        return Class::all();
    }

    /**
     * @OA\Post(
     *      path="/classes",
     *      operationId="storeClass",
     *      tags={"Classes"},
     *      summary="Store a newly created class",
     *      description="Stores a new class and returns the created class",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "school_id"},
     *              @OA\Property(property="name", type="string", example="JSS1"),
     *              @OA\Property(property="school_id", type="string", format="uuid", example="9a7a7b0e-0b1c-4f0e-8c1a-0b1c4f0e8c1a")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
            'school_id' => 'required|exists:schools,id',
        ]);

        $class = Class::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'school_id' => $request->school_id,
        ]);

        return response()->json($class, 201);
    }

    /**
     * @OA\Get(
     *      path="/classes/{id}",
     *      operationId="getClassById",
     *      tags={"Classes"},
     *      summary="Get class information",
     *      description="Returns class data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function show(string $id)
    {
        return Class::findOrFail($id);
    }

    /**
     * @OA\Put(
     *      path="/classes/{id}",
     *      operationId="updateClass",
     *      tags={"Classes"},
     *      summary="Update an existing class",
     *      description="Updates a class and returns the updated class",
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="JSS1")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function update(Request $request, string $id)
    {
        $class = Class::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where(function ($query) use ($request, $class) {
                    return $query->where('school_id', $class->school_id)->where('id', '!=', $class->id);
                }),
            ],
        ]);

        $class->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($class);
    }

    /**
     * @OA\Delete(
     *      path="/classes/{id}",
     *      operationId="deleteClass",
     *      tags={"Classes"},
     *      summary="Delete an existing class",
     *      description="Deletes a class and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       )
     * )
     */
    public function destroy(string $id)
    {
        $class = Class::findOrFail($id);

        if ($class->class_arms()->exists() || $class->students()->exists()) {
            return response()->json(['error' => 'Cannot delete class with associated arms or students.'], 422);
        }

        $class->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *      path="/classes/{classId}/arms",
     *      operationId="getClassArmsList",
     *      tags={"Class Arms"},
     *      summary="Get list of class arms",
     *      description="Returns list of class arms for a given class",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     *     )
     */
    public function indexArms(string $classId)
    {
        $class = Class::findOrFail($classId);
        return $class->class_arms;
    }

    /**
     * @OA\Post(
     *      path="/classes/{classId}/arms",
     *      operationId="storeClassArm",
     *      tags={"Class Arms"},
     *      summary="Store a newly created class arm",
     *      description="Stores a new class arm and returns the created class arm",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="A")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       )
     * )
     */
    public function storeArm(Request $request, string $classId)
    {
        $class = Class::findOrFail($classId);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_arms')->where(function ($query) use ($class) {
                    return $query->where('class_id', $class->id);
                }),
            ],
        ]);

        $arm = $class->class_arms()->create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($arm, 201);
    }

    /**
     * @OA\Get(
     *      path="/classes/{classId}/arms/{armId}",
     *      operationId="getClassArmById",
     *      tags={"Class Arms"},
     *      summary="Get class arm information",
     *      description="Returns class arm data",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function showArm(string $classId, string $armId)
    {
        $class = Class::findOrFail($classId);
        return $class->class_arms()->findOrFail($armId);
    }

    /**
     * @OA\Put(
     *      path="/classes/{classId}/arms/{armId}",
     *      operationId="updateClassArm",
     *      tags={"Class Arms"},
     *      summary="Update an existing class arm",
     *      description="Updates a class arm and returns the updated class arm",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="A"),
     *              @OA\Property(property="color", type="string", example="Blue")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function updateArm(Request $request, string $classId, string $armId)
    {
        $class = Class::findOrFail($classId);
        $arm = $class->class_arms()->findOrFail($armId);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_arms')->where(function ($query) use ($class, $arm) {
                    return $query->where('class_id', $class->id)->where('id', '!=', $arm->id);
                }),
            ],
        ]);

        $arm->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($arm);
    }

    /**
     * @OA\Delete(
     *      path="/classes/{classId}/arms/{armId}",
     *      operationId="deleteClassArm",
     *      tags={"Class Arms"},
     *      summary="Delete an existing class arm",
     *      description="Deletes a class arm and returns no content",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       )
     * )
     */
    public function destroyArm(string $classId, string $armId)
    {
        $class = Class::findOrFail($classId);
        $arm = $class->class_arms()->findOrFail($armId);

        if ($arm->students()->exists()) {
            return response()->json(['error' => 'Cannot delete arm with associated students.'], 422);
        }

        $arm->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *      path="/classes/{classId}/arms/{armId}/sections",
     *      operationId="getClassArmSectionsList",
     *      tags={"Class Arm Sections"},
     *      summary="Get list of class arm sections",
     *      description="Returns list of class arm sections for a given class arm",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     *     )
     */
    public function indexSections(string $classId, string $armId)
    {
        $arm = Class::findOrFail($classId)->class_arms()->findOrFail($armId);
        return $arm->class_sections;
    }

    /**
     * @OA\Post(
     *      path="/classes/{classId}/arms/{armId}/sections",
     *      operationId="storeClassArmSection",
     *      tags={"Class Arm Sections"},
     *      summary="Store a newly created class arm section",
     *      description="Stores a new class arm section and returns the created class arm section",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Science")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation"
     *       )
     * )
     */
    public function storeSection(Request $request, string $classId, string $armId)
    {
        $arm = Class::findOrFail($classId)->class_arms()->findOrFail($armId);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_sections')->where(function ($query) use ($arm) {
                    return $query->where('class_arm_id', $arm->id);
                }),
            ],
        ]);

        $section = $arm->class_sections()->create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($section, 201);
    }

    /**
     * @OA\Get(
     *      path="/classes/{classId}/arms/{armId}/sections/{sectionId}",
     *      operationId="getClassArmSectionById",
     *      tags={"Class Arm Sections"},
     *      summary="Get class arm section information",
     *      description="Returns class arm section data",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sectionId",
     *          description="Class Arm Section id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function showSection(string $classId, string $armId, string $sectionId)
    {
        $arm = Class::findOrFail($classId)->class_arms()->findOrFail($armId);
        return $arm->class_sections()->findOrFail($sectionId);
    }

    /**
     * @OA\Put(
     *      path="/classes/{classId}/arms/{armId}/sections/{sectionId}",
     *      operationId="updateClassArmSection",
     *      tags={"Class Arm Sections"},
     *      summary="Update an existing class arm section",
     *      description="Updates a class arm section and returns the updated class arm section",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sectionId",
     *          description="Class Arm Section id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Science")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function updateSection(Request $request, string $classId, string $armId, string $sectionId)
    {
        $arm = Class::findOrFail($classId)->class_arms()->findOrFail($armId);
        $section = $arm->class_sections()->findOrFail($sectionId);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('class_sections')->where(function ($query) use ($arm, $section) {
                    return $query->where('class_arm_id', $arm->id)->where('id', '!=', $section->id);
                }),
            ],
        ]);

        $section->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($section);
    }

    /**
     * @OA\Delete(
     *      path="/classes/{classId}/arms/{armId}/sections/{sectionId}",
     *      operationId="deleteClassArmSection",
     *      tags={"Class Arm Sections"},
     *      summary="Delete an existing class arm section",
     *      description="Deletes a class arm section and returns no content",
     *      @OA\Parameter(
     *          name="classId",
     *          description="Class id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="armId",
     *          description="Class Arm id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sectionId",
     *          description="Class Arm Section id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation"
     *       )
     * )
     */
    public function destroySection(string $classId, string $armId, string $sectionId)
    {
        $arm = Class::findOrFail($classId)->class_arms()->findOrFail($armId);
        $section = $arm->class_sections()->findOrFail($sectionId);

        if ($section->students()->exists()) {
            return response()->json(['error' => 'Cannot delete section with associated students.'], 422);
        }

        $section->delete();

        return response()->json(null, 204);
    }
}
