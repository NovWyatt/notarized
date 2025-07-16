<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'browser',
        'device',
        'platform',
        'location',
        'login_at',
        'last_activity',
        'logout_at',
        'is_active'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLocationAttribute($value)
    {
        return $value ?? 'Unknown';
    }

    public function getDurationAttribute()
    {
        if (!$this->logout_at) {
            return $this->login_at->diffForHumans();
        }
        return $this->login_at->diffForHumans($this->logout_at);
    }
}
