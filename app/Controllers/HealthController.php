<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;

class HealthController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            Database::getInstance();

            http_response_code(200);

            echo json_encode([
                'status' => 'ok',
                'database' => 'connected'
            ]);
        } catch (\Throwable $exception) {
            http_response_code(503);

            echo json_encode([
                'status' => 'error',
                'database' => 'unavailable'
            ]);
        }
    }
}
