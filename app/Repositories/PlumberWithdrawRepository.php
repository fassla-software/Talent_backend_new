<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\PlumberWithdraw;

class PlumberWithdrawRepository extends Repository
{
    public static function model()
    {
        return PlumberWithdraw::class;    
    }
}