<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\Request;

class RequestRepository extends Repository
{
    public static function model()
    {
        return Request::class;    
    }
}