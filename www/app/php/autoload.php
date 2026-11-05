<?php

declare(strict_types=1);

/**
 * PSR-4 autoloader: Unimarc2Marc21\Foo\Bar => APP_PHP_PATH/Foo/Bar.php
 * (the runtime image has no Composer, so the app ships its own).
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'Unimarc2Marc21\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $fileName = $prefix
            |> strlen(...)
            |> (fn($x) => substr($class, $x))
            |> (fn($x) => str_replace('\\', '/', $x));
    $file = APP_PHP_PATH . '/' . $fileName . '.php';

    if (is_file($file)) {
        require $file;
    }
});
