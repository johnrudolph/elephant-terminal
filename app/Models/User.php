<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\States\UserState;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSnowflakes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function state(): UserState
    {
        return UserState::load($this->id);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function initiatedFriendships()
    {
        return $this->hasMany(Friendship::class, 'initiator_id');
    }

    public function receivedFriendships()
    {
        return $this->hasMany(Friendship::class, 'recipient_id');
    }

    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'initiator_id')
            ->orWhere('recipient_id', $this->id);
    }

    public function friends()
    {
        return User::whereIn('id', function($query) {
            $query->select('recipient_id')
                ->from('friendships')
                ->where('initiator_id', $this->id)
                ->where('status', 'accepted')
            ->union(
                $query->newQuery()
                    ->select('initiator_id')
                    ->from('friendships')
                    ->where('recipient_id', $this->id)
                    ->where('status', 'accepted')
            );
        });
    }
}
