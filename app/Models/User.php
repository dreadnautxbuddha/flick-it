<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static \Illuminate\Database\Eloquent\Builder findByFlickrId(string $getId)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'flickr_id',
        'name',
        'nickname',
        'email',
        'flickr_token',
        'flickr_refresh_token',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Looks for a user by its `flickr_id` attribute.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param                                       $flickrId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByFlickrId(Builder $query, $flickrId): Builder
    {
        return $query->where('flickr_id', $flickrId);
    }
}
