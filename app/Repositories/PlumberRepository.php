<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\Plumber;

class PlumberRepository extends Repository
{
    public static function model()
    {
        return Plumber::class;    
    }
}