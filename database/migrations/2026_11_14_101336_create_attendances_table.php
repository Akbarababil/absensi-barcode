
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->date('attendance_date');
            $table->time('check_in')->nullable();

            $table->enum('status', [
                'hadir',
                'terlambat',
                'izin',
                'sakit'
            ])->default('hadir');

            $table->timestamps();

            $table->unique(
                ['student_id', 'attendance_date'],
                'unique_student_attendance'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
