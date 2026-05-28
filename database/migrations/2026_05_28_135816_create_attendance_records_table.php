<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date')->index();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'excused']);
            $table->foreignId('source_event_id')->nullable()->constrained('attendance_events')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'attendance_date', 'school_class_id'], 'attendance_unique_daily');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
