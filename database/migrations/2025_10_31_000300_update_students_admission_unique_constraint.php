<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $shouldDropOldUnique = $this->indexExists('students', 'students_admission_no_unique');
        $shouldCreateNewUnique = ! $this->indexExists('students', 'students_school_id_admission_no_unique');

        Schema::table('students', function (Blueprint $table) use ($shouldDropOldUnique, $shouldCreateNewUnique) {
            if ($shouldDropOldUnique) {
                $table->dropUnique('students_admission_no_unique');
            }

            if ($shouldCreateNewUnique) {
                $table->unique(['school_id', 'admission_no'], 'students_school_id_admission_no_unique');
            }
        });
    }

    public function down(): void
    {
        $shouldDropComposite = $this->indexExists('students', 'students_school_id_admission_no_unique');
        $shouldRestoreLegacy = ! $this->indexExists('students', 'students_admission_no_unique');

        Schema::table('students', function (Blueprint $table) use ($shouldDropComposite, $shouldRestoreLegacy) {
            if ($shouldDropComposite) {
                $table->dropUnique('students_school_id_admission_no_unique');
            }

            if ($shouldRestoreLegacy) {
                $table->unique('admission_no', 'students_admission_no_unique');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $databaseName = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT COUNT(*) AS count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$databaseName, $table, $indexName]
        );

        return (int) ($result->count ?? 0) > 0;
    }
};
