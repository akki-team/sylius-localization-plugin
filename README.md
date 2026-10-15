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

All options, with their default values:

```yaml
# config/packages/akki_sylius_localization_plugin.yaml

akki_sylius_localization:
    # Shared cache of the database translations (Redis for instance in a multi-server setup).
    cache: cache.app
    local_cache:
        # Keeps each translation in memory for the rest of the request (reset between two
        # Messenger messages), in front of the shared cache. Required by apcu.
        enabled: false
        apcu:
            # Also keeps a copy in the APCu of each server (local_cache.enabled must be true). Changes made through the plugin
            # (admin edition, deletion, import, akki:translations:load) are visible on every
            # server from the next request: the copies are keyed by a version token stored in
            # the shared cache. Ignored when APCu is missing or disabled (CLI by default).
            enabled: false
            # Lifetime (seconds) of the APCu copies: bounds the delay of an invalidation made
            # outside of the plugin (manual deletion of a shared cache entry).
            ttl: 300
```

With Redis as shared cache, every displayed translation costs up to 2 network round trips
without a local cache. On a page with a few hundred texts, `local_cache.apcu` removes most of
the cache traffic. Size APCu accordingly (`apc.shm_size`).

After a translation was changed outside of the plugin (direct SQL), clear the shared cache pool:
the cached values have no lifetime, and the version token goes with them.

```bash
$ php bin/console cache:pool:clear <your_cache_pool>
```

If you only deleted a shared cache entry by hand, also delete the version token, so that every
server drops its APCu copies at once instead of after `local_cache.apcu.ttl`:

```bash
$ php bin/console cache:pool:delete <your_cache_pool> akki_localization_version
```

## Upgrade

### To 1.3 / 2.2

- `DatabaseTranslator` resolves the channel on every translation again (1.2.0 / 2.1.0 kept it
  until `kernel.reset`): a process that switches channels, such as a command looping over
  channels with a settable channel context, translates with the current channel. Outside an
  HTTP request, the channel context is called for each translated message: if it queries the
  database, make it cheap, for instance by setting the channel explicitly.
- `LocalizationCacheVersion` and `LocalizationCacheInvalidator` are injected through
  `LocalizationCacheVersionInterface` and `LocalizationCacheInvalidatorInterface`: decorate or
  replace them through these service ids.
- `local_cache.enabled` is now off by default: set it to `true` to keep the request memory.
  `local_cache.apcu` requires it: enabling APCu alone is a configuration error.
- `LocalizedEntryCacheClearerInterface::clearMany()` clears several entries with a single change
  of the version token (imports). A custom implementation of the interface must add it.

### To 1.2 / 2.1

- A lookup index is added on `akki_localization_entry (channel_id, entry_key, entry_domain)`:
  generate and run a migration (`doctrine:migrations:diff`, then `doctrine:migrations:migrate`).
- The cache of a translation whose id contains a reserved character (`{}()/\@:`, for instance
  the `validators` messages) is now cleared; deleting a translation in the admin clears it too,
  and every shop locale is cleared (a locale without translation row caches the fallback value).
- `DatabaseTranslator` no longer keeps the channel of the first message for the whole process,
  and the request memory is reset by `kernel.reset` (between two Messenger messages).
- `akki:translations:load` now clears the imported translations from the cache, and with
  `--force`, every erased translation.
- `local_cache` (request memory, APCu copies) is opt-in.

## ⚠️ Warning 

It is recommended to use the command `akki:translations:load` after each (or at each) deployment to import new translations.
