<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if ($layout === null) {
            require $viewPath;
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../Views/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }
}
