<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('recurrence', ['none', 'monthly', 'yearly'])->default('none');
            $table->timestamps();

            $table->index('user_id');
            $table->index('due_date');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
