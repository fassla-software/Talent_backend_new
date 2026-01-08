<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\PlumberReceivedGift;

class PlumberReceivedGiftRepository extends Repository
{
    public static function model()
    {
        return PlumberReceivedGift::class;    
    }
}