<?php

use App\Http\Traits\AuditColumnsTrait;
use App\Models\Content;
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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sort_order')->default(0);

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('category');

            $table->tinyInteger('type')->default(Content::TYPE_STUDY_GUIDE)->comment('0 = study guides, 1 = flashcards');
            $table->boolean('is_publish')->default(Content::NOT_PUBLISH)->comment('0 = not publish, 1 = publish');
            
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
        Schema::dropIfExists('contents');
    }
};
