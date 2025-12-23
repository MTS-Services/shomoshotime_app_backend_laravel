<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Property;
use App\Models\PropertyImage;
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
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);


            $table->unsignedBigInteger('property_id');
            $table->tinyInteger('type')->default(PropertyImage::TYPE_IMAGE)->comment('1 = image, 2 = video');
            $table->boolean('is_primary')->default(PropertyImage::NOT_PRIMARY);
            $table->string('file');

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);


            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
