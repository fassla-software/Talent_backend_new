<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\PlumberCategory;

class PlumberCategoryRepository extends Repository
{
    public static function model()
    {
        return PlumberCategory::class;    
    }
}