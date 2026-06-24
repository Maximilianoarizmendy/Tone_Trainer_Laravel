<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('contact_requests')) {
            Schema::create('contact_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id');
                $table->unsignedBigInteger('receiver_id');
                $table->string('status')->default('pending'); // pending, accepted
                $table->timestamps();

                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->unique(['sender_id', 'receiver_id']);
            });
        }

        // Migrate existing message conversations to accepted contacts
        if (Schema::hasTable('messages') && Schema::hasTable('contact_requests')) {
            $existingConversations = DB::table('messages')
                ->select('sender_id', 'receiver_id')
                ->groupBy('sender_id', 'receiver_id')
                ->get();

            foreach ($existingConversations as $conv) {
                if ($conv->sender_id == $conv->receiver_id) {
                    continue;
                }

                // Check if request already exists in either direction
                $exists = DB::table('contact_requests')
                    ->where(function($q) use ($conv) {
                        $q->where('sender_id', $conv->sender_id)->where('receiver_id', $conv->receiver_id);
                    })
                    ->orWhere(function($q) use ($conv) {
                        $q->where('sender_id', $conv->receiver_id)->where('receiver_id', $conv->sender_id);
                    })
                    ->exists();

                if (!$exists) {
                    try {
                        DB::table('contact_requests')->insert([
                            'sender_id' => $conv->sender_id,
                            'receiver_id' => $conv->receiver_id,
                            'status' => 'accepted',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        // Safe ignore
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
