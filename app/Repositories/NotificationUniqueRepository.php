<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\NotificationUnique;

class NotificationUniqueRepository extends Repository
{
    public static function model()
    {
        return NotificationUnique::class;    
    }
}