<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('iam.tables.model_has_roles'), function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained(config('iam.tables.roles'))
                ->cascadeOnDelete();

            $table->morphs('model');

            $table->primary(['role_id', 'model_id', 'model_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('iam.tables.model_has_roles'));
    }
};
