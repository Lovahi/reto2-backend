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
        
        if (!\file_exists($filePath)) {
            return "";
        }

        return $imageName;
    }

    /**
     * Valida y guarda una imagen subida.
     * 
     * @param array|null $file El array $_FILES['campo']
     * @param string $subFolder Carpeta de destino dentro de public/img/
     * @return string Nombre del archivo guardado o vacío si falla.
     */
    public static function saveImage(?array $file, string $subFolder): string {
        if (!$file || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return "";
        }

        $targetDir = __DIR__ . "/../../public/img/{$subFolder}/";
        
        if (!\is_dir($targetDir)) {
            \mkdir($targetDir, 0777, true);
        }

        // 1. Validar MIME real
        $finfo = \finfo_open(FILEINFO_MIME_TYPE);
        $mime = \finfo_file($finfo, $file['tmp_name']);
        \finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!\in_array($mime, $allowedMimes)) {
            throw new \Exception("The uploaded file is not a valid image ({$mime}).");
        }

        // 2. Añadimos el nombre archivo
        $baseName = \pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = \pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($extension)) {
            // Si no tiene extensión, la deducimos del MIME
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif'
            ];
            $extension = $extensions[$mime] ?? 'jpg';
        }
        $filename = \str_replace(' ', '-', $baseName) . ".{$extension}";
        $targetPath = "{$targetDir}{$filename}";

        // 3. Mover el archivo
        if (\move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename;
        }

        return "";
    }
}
