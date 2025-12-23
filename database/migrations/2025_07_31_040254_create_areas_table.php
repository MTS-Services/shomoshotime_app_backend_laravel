<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Area;
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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->string('name');
            $table->string('slug')->unique();
            $table->tinyInteger('status')->default(Area::STATUS_INACTIVE)->comment('0: Inactive, 1: active');

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
        Schema::dropIfExists('areas');
    }
};
