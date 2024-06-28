<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_digest')->nullable()->after('password');
            $table->boolean('activated')->default(false)->after('activation_digest');
            $table->timestamp('activated_at')->nullable()->after('activated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activated_at');
            $table->dropColumn('activated');
            $table->dropColumn('activation_digest');
        });
    }
};
