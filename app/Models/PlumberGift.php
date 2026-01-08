<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlumberGift extends Model
{
    use HasFactory;

	protected $guarded = ['id'];

	protected $table = 'plumber_gifts';

	// public function received_gifts(): HasMany
	// {
	// return $this->hasMany(PlumberReceivedGift::class, 'gifts_id');
	// }
}