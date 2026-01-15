<?php

namespace App\Helper;

class Thumbnail
{
    public static function getThumbnail(string $source, string $destinationPath, string $thumbnailName, int $quality = 2): bool
    {
        // Use Intervention Image v3
        $image = \Intervention\Image\Laravel\Facades\Image::read($source);
        $image->resize(150, 150); // Simplified for v3
        $image->save($destinationPath . $thumbnailName, $quality * 25);
        return true;
    }
}
