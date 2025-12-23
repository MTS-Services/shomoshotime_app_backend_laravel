<?php

namespace App\Http\Traits;

use Illuminate\Database\Schema\Blueprint;

trait AuditColumnsTrait
{
    public function addAuditColumns(Blueprint $table): void
    {
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();

        $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

        $table->index('created_by');
        $table->index('updated_by');
    }

    public function dropAuditColumns(Blueprint $table): void
    {
        $table->dropForeign(['created_by']);
        $table->dropForeign(['updated_by']);

        $table->dropColumn('created_by');
        $table->dropColumn('updated_by');
    }
}
