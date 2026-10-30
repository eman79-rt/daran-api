<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $t) {
            $t->id();
            $t->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $t->foreignId('province_id')->constrained()->restrictOnDelete();
            $t->string('title', 180);
            $t->text('description');
            $t->string('type', 30);
            $t->string('offer_type', 10)->index();
            $t->string('status', 15)->default('pending')->index();
            $t->decimal('price_amount', 15, 2);
            $t->string('currency', 3);
            $t->string('address');
            $t->unsignedTinyInteger('bedrooms')->nullable();
            $t->unsignedTinyInteger('bathrooms')->nullable();
            $t->decimal('area_sqm', 10, 2);
            $t->string('owner_phone', 30);
            $t->decimal('commission_percent', 5, 2)->nullable();
            $t->text('internal_notes')->nullable();
            $t->boolean('is_featured')->default(false);
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
            $t->index(['province_id', 'offer_type', 'price_amount']);
            $t->index(['status', 'approved_at', 'created_at', 'id']);
            $t->index(['agency_id', 'created_at', 'id']);
        });
        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->morphs('mediable');
            $t->string('path');
            $t->string('collection', 30)->default('property');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->index(['collection', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('properties');
    }
};
