<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(config('iam.tables.role_has_permissions'), function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained(config('iam.tables.roles'))
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained(config('iam.tables.permissions'))
                ->cascadeOnDelete();

            $table->primary(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('iam.tables.role_has_permissions'));
    }
};
