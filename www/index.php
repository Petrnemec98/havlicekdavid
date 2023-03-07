<?php

declare(strict_types=1);
ini_set('max_execution_time', '180');
ini_set('max_file_uploads', '1000');
ini_set('upload_max_filesize', '2048M');


require __DIR__ . '/../vendor/autoload.php';

$configurator = App\Bootstrap::boot();
$container = $configurator->createContainer();
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
