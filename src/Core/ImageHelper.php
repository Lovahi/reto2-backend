<?php

namespace App\Core;

class ImageHelper {
    /**
     * @param string|null $imageName Nombre del archivo en la BBDD.
     * @param string $subFolder Carpeta dentro de /public/img/.
     */
    public static function getImageUrl(?string $imageName, string $subFolder): string {
        if (!$imageName) return "";
        
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $subFolder . DIRECTORY_SEPARATOR . $imageName;
        
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
        if (!$file || !isset($file['tmp_name'])) {
            return "";
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            $message = $errors[$file['error']] ?? 'Unknown upload error';
            throw new \Exception("Upload error: " . $message);
        }

        $targetDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $subFolder . DIRECTORY_SEPARATOR;
        
        if (!\is_dir($targetDir)) {
            if (!\mkdir($targetDir, 0777, true)) {
                throw new \Exception("Could not create directory {$targetDir}");
            }
        }

        if (!\is_writable($targetDir)) {
            throw new \Exception("Directory {$targetDir} is not writable.");
        }

        // 1. Validar MIME real
        $finfo = \finfo_open(FILEINFO_MIME_TYPE);
        $mime = \finfo_file($finfo, $file['tmp_name']);
        \finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!\in_array($mime, $allowedMimes)) {
            throw new \Exception("The uploaded file is not a valid image ({$mime}).");
        }

        // 2. Generar nombre de archivo único
        $baseName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', \pathinfo($file['name'], PATHINFO_FILENAME));
        $extension = \pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif'
            ];
            $extension = $extensions[$mime] ?? 'jpg';
        }

        $filename = uniqid() . '-' . $baseName . ".{$extension}";
        $targetPath = $targetDir . $filename;

        // 3. Mover el archivo
        if (\move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename;
        }

        throw new \Exception("Failed to move uploaded file to {$targetPath}. Check directory permissions.");
    }
}
