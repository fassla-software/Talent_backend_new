<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\InspectionRequestItem;

class InspectionRequestItemRepository extends Repository
{
    public static function model()
    {
        return InspectionRequestItem::class;    
    }
}