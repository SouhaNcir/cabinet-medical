<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../Views/layouts/' . $layout . '.php';

        if (!file_exists($viewPath) || !file_exists($layoutPath)) {
            http_response_code(500);
            echo 'View or layout missing.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
    }
}
