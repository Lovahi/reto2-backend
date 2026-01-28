<?php

namespace App\Core;

class ImageHelper {
    /**
     * Construye la URL completa para una imagen.
     * @param string|null $imageName Nombre del archivo en la BBDD.
     * @param string $subFolder Carpeta dentro de /public/img/.
     * @return string URL absoluta.
     */
    public static function getImageUrl(?string $imageName, string $subFolder): string {
        if (!$imageName) return "";

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return "{$protocol}{$host}/img/{$subFolder}/{$imageName}";
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
        
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . "." . $extension;
        
        if (move_uploaded_file($image['tmp_name'], "{$targetDir}{$filename}")) {
            return $filename;
        }
        
        return "";
    }
}
