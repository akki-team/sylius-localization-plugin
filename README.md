# AkkiSyliusTranslationPlugin

## Overview

## Installation

1. Install the plugin to your project with the following command:

```bash
$ composer require akki-team/sylius-translation-plugin
```

2. After the installation, check that the plugin is correctly declared in your project in the file `config/bundles.php`.

```php

 return [
    ...
    Akki\SyliusTranslationPlugin\AkkiSyliusLocalizationPlugin::class => ['all' => true],
];
 ```
