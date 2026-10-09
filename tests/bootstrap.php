<?php
declare(strict_types=1);

// Autoloader du plugin (vendor/), ou celui d'un projet hôte qui l'utilise : AKKI_PLUGIN_AUTOLOAD.
require getenv('AKKI_PLUGIN_AUTOLOAD') ?: __DIR__ . '/../vendor/autoload.php';

// Les classes du plugin et des tests sont chargées depuis ce dépôt, avant celles d'un éventuel
// projet hôte (dont l'autoloader peut contenir une autre version du plugin).
spl_autoload_register(static function (string $class): void {
    foreach (['Akki\\SyliusLocalizationPlugin\\Tests\\' => __DIR__ . '/', 'Akki\\SyliusLocalizationPlugin\\' => __DIR__ . '/../src/'] as $prefix => $dir) {
        if (true === str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, \strlen($prefix))) . '.php';

            if (true === is_file($file)) {
                require $file;
            }

            return;
        }
    }
}, true, true);
