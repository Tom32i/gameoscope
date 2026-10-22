<?php

declare(strict_types=1);

$files = glob(\dirname(__DIR__) . '/var/cache/prod/*.preload.php');

foreach (false !== $files ? $files : [] as $file) {
    require $file;
}
