<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPlumberNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_plumber_notifications';

    protected $fillable = ['title', 'subject', 'message', 'firebase_id', 'data', 'read'];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean'
    ];

}
