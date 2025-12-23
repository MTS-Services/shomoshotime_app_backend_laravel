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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);

            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id');


            $table->timestamp('joined_at');
            $table->boolean('is_muted')->default(false);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['conversation_id', 'user_id'], 'participants_conversation_user_unique');

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
        Schema::dropIfExists('participants');
    }
};
