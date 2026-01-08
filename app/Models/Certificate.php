<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificate';

    protected $fillable = [
        'certificate_id', 'user_phone', 'file_name', 'created_at', 'updated_at', 'nationality_id', 'plumber_id'
    ];

    // Define relationship with Users table (since plumber_id references users.id)
    public function plumber()
    {
        return $this->belongsTo(User::class, 'plumber_id');
    }

    // Accessor to generate the full file URL
    public function getFileUrlAttribute()
    {
        if ($this->file_name) {
            return "https://app.talentindustrial.com/plumber/PDF/{$this->file_name}";
        }
        return null; // Return null if no file_name exists
    }
}
