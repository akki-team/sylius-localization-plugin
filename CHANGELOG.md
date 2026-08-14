# Changelog

## [2.0.1] - 2026-08-14
### :bug: Bug Fixes
- [`1cb9a1e`](https://github.com/akki-team/sylius-localization-plugin/commit/1cb9a1e05f9826dbe6557df326d9bdd34a817855) - types de retour SF7 sur DatabaseTranslator *(commit by [@severine-akki](https://github.com/severine-akki))*
- [`d225ab4`](https://github.com/akki-team/sylius-localization-plugin/commit/d225ab47751fc8f5c4e20afe74278597e10229a8) - initialise la locale de LocalizedEntry avant getTranslation *(commit by [@severine-akki](https://github.com/severine-akki))*


## [2.0.0] - 2026-08-11
### :sparkles: New Features
- [`84217cb`](https://github.com/akki-team/sylius-localization-plugin/commit/84217cb1d843126d7c58e327be36003e706ea7ea) - compatibilité Sylius 2 / PHP 8.2 *(commit by [@severine-akki](https://github.com/severine-akki))*


## [1.1.2](https://github.com/akki-team/sylius-localization-plugin/tree/1.1.2) (2025-05-25)

- fix: Correction d'un bug où le database translator n'utilise pas la bonne clé de service ce qui peut faire perdre une potentielle décoration

## [1.1.1](https://github.com/akki-team/sylius-localization-plugin/tree/1.1.1) (2025-01-29)

- Fixed a bug where data was always overwritten during the translation import if it already existed.

## [1.1.0](https://github.com/akki-team/sylius-localization-plugin/tree/1.1.0) (2024-12-16)

- feat: LocalizedEntry grid is now created and managed on php.

## [1.0.0](https://github.com/akki-team/sylius-localization-plugin/tree/1.0.0) (2024-12-03)

- Start plugin with 1.0.0 version
- Allow Sylius developer to manage translation in back-office.
- Use cache system to improve performance.
[2.0.0]: https://github.com/akki-team/sylius-localization-plugin/compare/1.1.2...2.0.0
[2.0.1]: https://github.com/akki-team/sylius-localization-plugin/compare/2.0.0...2.0.1
