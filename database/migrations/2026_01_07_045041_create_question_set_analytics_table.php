<?php

use App\Http\Traits\AuditColumnsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use SoftDeletes, AuditColumnsTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_set_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_set_id');

            // Practice Mode Tracking
            $table->integer('practice_questions_answered')->default(0); 
            $table->integer('practice_correct_answers')->default(0);
            $table->boolean('practice_completed')->default(false);
            $table->timestamp('practice_completed_at')->nullable();

            // Mock Test Tracking
            $table->integer('mock_test_attempts')->default(0); 
            $table->integer('current_mock_questions_answered')->default(0);
            $table->integer('best_mock_score')->default(0);
            $table->decimal('best_mock_percentage', 5, 2)->default(0);

            $table->unique(['user_id', 'question_set_id']);            
           
            $table->enum('current_mode', ['practice', 'mock_test'])->default('practice');
            $table->integer('current_mock_attempt_number')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->cascadeOnDelete()->cascadeOnUpdate();

            
            $table->softDeletes();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_set_analytics');
    }
};
