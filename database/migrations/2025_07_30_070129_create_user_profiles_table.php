<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\UserProfile;
use App\Models\UserProfiles;
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->unsignedBigInteger('user_id');

            $table->date('dob')->nullable();
            $table->tinyInteger('gender')->default(UserProfile::GENDER_OTHER)->nullable()->comment('0: other, 1: male, 2: female | ٠: آخر, ١: ذكر, ٢: أنثى');

            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->string('postal_code')->nullable();

            $table->text('bio')->nullable();

            $table->string('website')->nullable();
            $table->json('social_links')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
