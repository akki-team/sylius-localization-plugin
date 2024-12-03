# AkkiSyliusLocalizationPlugin

## Overview

## Installation

1. Install the plugin to your project with the following command:

```bash
$ composer require akki-team/sylius-localization-plugin
```

2. After the installation, check that the plugin is correctly declared in your project in the file `config/bundles.php`.

```php

 return [
    ...
    Akki\SyliusLocalizationPlugin\AkkiSyliusLocalizationPlugin::class => ['all' => true],
];
 ```

3. Import config in your `config/packages/_sylius.yaml` file:
```yaml
# config/packages/_sylius.yaml

imports:
    ...
    
    - { resource: "@AkkiSyliusLocalizationPlugin/Resources/config/config.yaml" }
```

4. Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

akki_sylius_localization_plugin:
  resource: "@AkkiSyliusLocalizationPlugin/Resources/config/routes.yaml"
```

5. Update your database

```bash
$ php bin/console cache:clear
$ php bin/console doctrine:migrations:diff
$ php bin/console doctrine:migrations:migrate
```

6. Import translations in database

```bash
$ php bin/console akki:translations:load
```

## Configuration

By default, ```@cache.app``` is used to put translations in cache. You can override with other an other cache :

```yaml
# config/akki_sylius_localization_plugin.yaml

akki_sylius_localization_plugin:
  cache: my_new_cache
```

## ⚠️ Warning 

It is recommended to use the command `akki:translations:load` after each (or at each) deployment to import new translations.
