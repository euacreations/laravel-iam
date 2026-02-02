<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('iam.tables.role_has_features'), function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained(config('iam.tables.roles'))
                ->cascadeOnDelete();

            $table->foreignId('feature_id')
                ->constrained(config('iam.tables.features'))
                ->cascadeOnDelete();

            $table->primary(['role_id', 'feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('iam.tables.role_has_features'));
    }
};
