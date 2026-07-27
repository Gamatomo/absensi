<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure `departments` table exists
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        // 2. Check and migrate `school_classes`
        if (Schema::hasColumn('school_classes', 'department')) {
            // First, add the department_id column if it doesn't exist
            if (!Schema::hasColumn('school_classes', 'department_id')) {
                Schema::table('school_classes', function (Blueprint $table) {
                    $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
                });
            }

            // Migrate data
            $classes = DB::table('school_classes')->whereNotNull('department')->where('department', '!=', '')->get();
            foreach ($classes as $class) {
                // Ensure department exists
                $deptId = DB::table('departments')->where('name', $class->department)->value('id');
                if (!$deptId) {
                    $deptId = DB::table('departments')->insertGetId([
                        'name' => $class->department,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Update school_classes with new department_id
                DB::table('school_classes')->where('id', $class->id)->update(['department_id' => $deptId]);
            }

            // Drop the old department column
            Schema::table('school_classes', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }

        // 3. Check and migrate `students`
        if (Schema::hasColumn('students', 'department')) {
            // First, add the department_id column if it doesn't exist
            if (!Schema::hasColumn('students', 'department_id')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
                });
            }

            // Migrate data
            $students = DB::table('students')->whereNotNull('department')->where('department', '!=', '')->get();
            foreach ($students as $student) {
                // Ensure department exists
                $deptId = DB::table('departments')->where('name', $student->department)->value('id');
                if (!$deptId) {
                    $deptId = DB::table('departments')->insertGetId([
                        'name' => $student->department,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Update students with new department_id
                DB::table('students')->where('id', $student->id)->update(['department_id' => $deptId]);
            }

            // Drop the old department column
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('school_classes', 'department_id')) {
            if (!Schema::hasColumn('school_classes', 'department')) {
                Schema::table('school_classes', function (Blueprint $table) {
                    $table->string('department')->nullable();
                });
            }

            $classes = DB::table('school_classes')->whereNotNull('department_id')->get();
            foreach ($classes as $class) {
                $deptName = DB::table('departments')->where('id', $class->department_id)->value('name');
                DB::table('school_classes')->where('id', $class->id)->update(['department' => $deptName]);
            }

            Schema::table('school_classes', function (Blueprint $table) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            });
        }

        if (Schema::hasColumn('students', 'department_id')) {
            if (!Schema::hasColumn('students', 'department')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('department')->nullable();
                });
            }

            $students = DB::table('students')->whereNotNull('department_id')->get();
            foreach ($students as $student) {
                $deptName = DB::table('departments')->where('id', $student->department_id)->value('name');
                DB::table('students')->where('id', $student->id)->update(['department' => $deptName]);
            }

            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            });
        }
    }
};
