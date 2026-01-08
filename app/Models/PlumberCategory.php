<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlumberCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Guard against mass assignment

    protected $table = 'plumber_categories';
    protected $fillable = ['name', 'parent_id', 'image', 'points', 'product_flag', 'created_at', 'updated_at'];


    /**
     * Get the subcategories for this category.
     */
    public function subcategories()
    {
        return $this->hasMany(PlumberCategory::class, 'parent_id');
    }
public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
public function activeSubcategories()
    {
        return $this->hasMany(PlumberCategory::class, 'parent_id')
                    ->where('product_flag', 0)
                    ->with(['activeSubcategories']); // Recursively include only active subcategories
    }
}
