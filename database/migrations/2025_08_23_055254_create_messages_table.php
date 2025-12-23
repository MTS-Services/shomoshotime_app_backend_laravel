<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Message;
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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);

            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();

            $table->longText('message_content')->nullable();
            $table->timestamp('send_at');
           

            $table->tinyInteger('status')->default(Message::STATUS_SENT);

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('messages');
    }
};
