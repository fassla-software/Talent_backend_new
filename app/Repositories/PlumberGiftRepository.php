<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\PlumberGift;

class PlumberGiftRepository extends Repository
{
    public static function model()
    {
        return PlumberGift::class;    
    }
}