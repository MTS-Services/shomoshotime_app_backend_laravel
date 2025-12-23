<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);

            $table->string('email', 255)->unique()->nullable();
            $table->string('phone')->unique();

            $table->string('password', 255);
            $table->string('name')->nullable();
            $table->string('image')->nullable();

            $table->tinyInteger('user_type')->default(User::USER_TYPE_INDIVIDUAL);
            $table->tinyInteger('language_preference')->default(User::LANGUAGE_ARABIC);
            $table->tinyInteger('status')->default(User::STATUS_ACTIVE);

            $table->string('otp', 4)->nullable();
            $table->dateTime('otp_sent_at')->nullable();
            $table->dateTime('otp_expires_at')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->boolean('is_admin')->default(User::NOT_ADMIN);
            $table->boolean('is_banned')->default(User::NOT_BANNED);

            $table->string('fcm_token')->nullable();
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
