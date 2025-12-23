<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Payment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use SoftDeletes, AuditColumnsTrait;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_id')->unique();
            $table->string('reference_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->decimal('amount', 15, 3);
            $table->string('currency', 3)->default('414');
            $table->string('language', 3)->default('ENG');
            $table->tinyInteger('status')->default(Payment::STATUS_PENDING);
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);

            $table->index(['status', 'transaction_id']);
            $table->index('reference_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
