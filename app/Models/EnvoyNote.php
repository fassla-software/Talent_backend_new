<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnvoyNote extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'envoy_notes';

    /**
     * Get the envoy that created the note.
     */
    public function envoy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'envoy_id');
    }

    /**
     * Get the client (plumber/trader) the note is about.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
