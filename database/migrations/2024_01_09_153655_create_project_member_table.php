<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named `project_member` to match the belongsToMany definitions on the
        // Project and Member models; the table was previously created as
        // `projects_members`, so every pivot query failed at runtime.
        Schema::create('project_member', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();

            // A member can only be on a project once; enforce it in the schema
            // rather than relying on an application-level check alone.
            $table->unique(['project_id', 'member_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_member');
    }
};
