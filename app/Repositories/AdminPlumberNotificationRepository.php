<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\AdminPlumberNotification;

class AdminPlumberNotificationRepository extends Repository
{
    public static function model()
    {
        return AdminPlumberNotification::class;    
    }
}