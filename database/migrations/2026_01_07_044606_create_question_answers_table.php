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
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_set_id');
            $table->unsignedBigInteger('question_id');

            // Practice Mode Stats
            $table->integer('practice_correct_attempts')->default(0);
            $table->integer('practice_failed_attempts')->default(0);
            $table->string('practice_last_answer')->nullable();
            $table->timestamp('practice_first_answered_at')->nullable();

            // Mock Test Stats
            $table->integer('mock_correct_attempts')->default(0);
            $table->integer('mock_failed_attempts')->default(0);
            
            // Current Answer Info
            $table->enum('last_mode', ['practice', 'mock_test'])->default('practice');
            $table->string('last_answer')->nullable();
            $table->integer('last_mock_attempt_number')->default(0);


            $table->unique(['user_id', 'question_set_id', 'question_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();

            
            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_answers');
    }
};
