<?php
namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\Image;

class ImageRepository extends Repository
{
    public static function model()
    {
        return Image::class;    
    }
}