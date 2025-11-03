<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubjectTeacherAssignment
 *
 * @property string $id
 * @property string $subject_id
 * @property string $staff_id
 * @property string|null $school_class_id
 * @property string|null $class_arm_id
 * @property string|null $class_section_id
 * @property string $session_id
 * @property string $term_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property SchoolClass|null $school_class
 * @property ClassArm|null $class_arm
 * @property ClassSection|null $class_section
 * @property Session $session
 * @property Staff $staff
 * @property Subject $subject
 * @property Term $term
 *
 * @package App\Models
 */
class SubjectTeacherAssignment extends Model
{
	protected $table = 'subject_teacher_assignments';
	public $incrementing = false;

	protected $keyType = 'string';

	use HasUuids;

	protected $fillable = [
		'id',
		'subject_id',
		'staff_id',
		'school_class_id',
		'class_arm_id',
		'class_section_id',
		'session_id',
		'term_id'
	];

	public function school_class()
	{
		return $this->belongsTo(SchoolClass::class, 'school_class_id');
	}

	public function class_arm()
	{
		return $this->belongsTo(ClassArm::class);
	}

	public function class_section()
	{
		return $this->belongsTo(ClassSection::class);
	}

	public function session()
	{
		return $this->belongsTo(Session::class);
	}

	public function staff()
	{
		return $this->belongsTo(Staff::class);
	}

	public function subject()
	{
		return $this->belongsTo(Subject::class);
	}

	public function term()
	{
		return $this->belongsTo(Term::class);
	}
}
