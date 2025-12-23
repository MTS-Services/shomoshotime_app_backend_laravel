<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Property;
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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);

            // Replacing foreignId with unsignedBigInteger
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();

            // Other columns
            $table->string('title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->tinyInteger('status')->default(Property::STATUS_OPEN);
            $table->boolean('is_featured')->default(Property::NOT_FEATURED);
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('renew_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);

            // Setting up the foreign keys manually
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete()->onUpdate('cascade');
            $table->foreign('property_type_id')->references('id')->on('property_types')->nullOnDelete()->onUpdate('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->nullOnDelete()->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
