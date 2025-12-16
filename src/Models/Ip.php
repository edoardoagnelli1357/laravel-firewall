<?php

namespace Edoardoagnelli1357\Firewall\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ip extends Model
{
    use SoftDeletes;

    protected $table = 'firewall_ips';

    protected $fillable = ['ip', 'user_agent', 'log_id', 'blocked'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function log()
    {
        return $this->belongsTo(Log::class, 'ip', 'ip');
    }

    public function scopeBlocked($query, $ip = null)
    {
        $q = $query->where('blocked', 1);

        if ($ip) {
            $q = $query->where('ip', $ip);
        }

        return $q;
    }
}
