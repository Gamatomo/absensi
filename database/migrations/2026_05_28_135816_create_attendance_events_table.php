<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rfid_uid')->nullable()->index();
            $table->enum('face_result', ['match', 'mismatch', 'not_provided'])->default('not_provided');
            $table->decimal('face_confidence', 5, 2)->nullable();
            $table->timestamp('captured_at')->index();
            $table->string('idempotency_key')->unique();
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
