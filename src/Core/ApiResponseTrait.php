<?php

namespace App\Core;

use PDOException;

trait ApiResponseTrait {
    private function jsonResponse(mixed $data, int $status = 200): void {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode($data);
    }

    private function getRequestInput(): array {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!\is_array($data)) {
            $data = [];
        }

        // Merge with $_POST and $_FILES to support form-data
        return [...$data, ...$_POST, ...$_FILES];
    }

    private function handleDatabaseException(PDOException $e): void {
        $errorCode = $e->getCode();
        $errorInfo = $e->errorInfo[1] ?? null;

        switch ($errorCode) {
            case '23000':
                if ($errorInfo === 1062) {
                    $this->jsonResponse(['error' => 'Duplicate entry: the resource already exists'], 400);
                } elseif ($errorInfo === 1451 || $errorInfo === 1452) {
                    $this->jsonResponse(['error' => 'Foreign key constraint violation'], 400);
                } else {
                    $this->jsonResponse(['error' => 'Integrity constraint violation'], 400);
                }
                break;
            case '42S02':
                $this->jsonResponse(['error' => 'Table not found in the database'], 500);
                break;
            case '42S22':
                $this->jsonResponse(['error' => 'Column not found in the database'], 500);
                break;
            case '22001':
                $this->jsonResponse(['error' => 'Data too long for one of the columns'], 400);
                break;
            case 'HY000':
                $this->jsonResponse(['error' => 'General database error or connection issue'], 500);
                break;
            default:
                $this->jsonResponse(['error' => 'A database error occurred: ' . $e->getMessage()], 500);
                break;
        }
    }
}
