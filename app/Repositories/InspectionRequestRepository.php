<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\InspectionRequest;

class InspectionRequestRepository extends Repository
{
    public static function model()
    {
        return InspectionRequest::class;    
    }
}