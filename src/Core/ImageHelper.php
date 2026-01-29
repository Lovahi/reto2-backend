<?php

namespace App\Core;

class ImageHelper {
    /**
     * @param string|null $imageName Nombre del archivo en la BBDD.
     * @param string $subFolder Carpeta dentro de /public/img/.
     */
    public static function getImageUrl(?string $imageName, string $subFolder): string {
        if (!$imageName) return "";
        
        $filePath = __DIR__ . "/../../public/img/{$subFolder}/{$imageName}";
        
        if (!file_exists($filePath)) {
            return "";
        }

        return $imageName;
    }

    public static function saveImage($image, $subFolder): string {
        if (!$image) return "";

        $targetDir = __DIR__ . "/../../public/img/{$subFolder}/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        if (\is_string($image) && !str_contains($image, '/')) {
            return $image;
        }
        
        $baseName = pathinfo($image['name'], PATHINFO_FILENAME);
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = str_replace(' ', '-', $baseName) . "." . $extension;
        
        if (move_uploaded_file($image['tmp_name'], "{$targetDir}{$filename}")) {
            return $filename;
        }
        
        return "";
    }
}
