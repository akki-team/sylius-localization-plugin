<?php
declare(strict_types=1);


use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// Configuration en PHP : Symfony 8 ne lit plus la configuration des services en XML.
return static function (ContainerConfigurator $container): void {
    $container->import(__DIR__ . '/services/*.php');
};
