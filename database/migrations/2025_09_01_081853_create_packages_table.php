<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Package;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);
            $table->string('name');
            $table->tinyInteger('tag')->nullable();
            $table->tinyInteger('status')->default(Package::STATUS_ACTIVE);
            $table->string('tag_color')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('total_ad')->default(0);
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
        Schema::dropIfExists('packages');
    }
};
