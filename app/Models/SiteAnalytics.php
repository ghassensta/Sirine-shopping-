<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'ip_address',
        'user_agent',
        'page',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}