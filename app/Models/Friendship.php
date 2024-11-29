<?php

namespace App\Models;

use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    use HasFactory, HasSnowflakes;

    protected $guarded = [];

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeBetweenUsers($query, $userA, $userB)
    {
        return $query->where(function($q) use ($userA, $userB) {
            $q->where([
                'initiator_id' => $userA,
                'recipient_id' => $userB
            ])->orWhere([
                'initiator_id' => $userB,
                'recipient_id' => $userA
            ]);
        });
    }
}
