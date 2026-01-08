<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\AdminNotification;

class AdminNotificationRepository extends Repository
{
    public static function model()
    {
        return AdminNotification::class;    
    }
}