# Changelog

## [2.1.0] - 2026-10-09
### :sparkles: New Features
- [`37d2005`](https://github.com/akki-team/sylius-localization-plugin/commit/37d2005fca97cecbd58de7c9e1b3b3f8893fd1ec) - cache local des traductions (mémoire de requête, APCu en option) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :bug: Bug Fixes
- [`b2544ad`](https://github.com/akki-team/sylius-localization-plugin/commit/b2544ad70c1d5fcb58edc85bf9c24e88149327ac) - vider le cache d'une traduction avec la clé utilisée à l'écriture, y compris à la suppression *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`53837d0`](https://github.com/akki-team/sylius-localization-plugin/commit/53837d0172e7bf841450cf0b8d19997da890fd0b) - akki:translations:load vide du cache les traductions importées ou effacées *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`79b0188`](https://github.com/akki-team/sylius-localization-plugin/commit/79b01889d5af2d7d71049f7a1b5aa8add04c465a) - DatabaseTranslator résout le canal à chaque appel *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`e95675f`](https://github.com/akki-team/sylius-localization-plugin/commit/e95675f03615436e711e3d7688875e7c817f5b33) - type nullable explicite sur getValue() (dépréciation PHP 8.4) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`e9ee810`](https://github.com/akki-team/sylius-localization-plugin/commit/e9ee8102491ecab9492e44e55338eba1da45b652) - DatabaseTranslator garde le canal jusqu'au prochain kernel.reset *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`45215f2`](https://github.com/akki-team/sylius-localization-plugin/commit/45215f2983c97e4e5bd804fce51bdbc2308c6294) - mémoire de requête bornée et jeton de version plus robuste *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`c61bcec`](https://github.com/akki-team/sylius-localization-plugin/commit/c61bcecf7e6c7d4240088dd5981b2f6a0d1f1604) - vider toutes les locales de la boutique, même si l'import échoue *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`327d43c`](https://github.com/akki-team/sylius-localization-plugin/commit/327d43c8f3726924dbd049d5f37844b11f353ef0) - configuration des services en PHP, compatible Symfony 8 *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :zap: Performance Improvements
- [`a0bf6b2`](https://github.com/akki-team/sylius-localization-plugin/commit/a0bf6b2d2b7805e014c08e68a7108f4edb4bded9) - index de recherche sur akki_localization_entry (canal, clé, domaine) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :white_check_mark: Tests
- [`2f8c426`](https://github.com/akki-team/sylius-localization-plugin/commit/2f8c4264996164c89df08863c292aaebc8c6d80b) - tests unitaires du cache et du traducteur *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*


## [1.2.0] - 2026-10-09
### :sparkles: New Features
- [`2d24053`](https://github.com/akki-team/sylius-localization-plugin/commit/2d24053ccd29c8c9f4515aa0d3b0b802ed15cd59) - cache local des traductions (mémoire de requête, APCu en option) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :bug: Bug Fixes
- [`451f3d6`](https://github.com/akki-team/sylius-localization-plugin/commit/451f3d67b3632f02cf02b046fdcf8a0acd43d914) - vider le cache d'une traduction avec la clé utilisée à l'écriture, y compris à la suppression *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`e5751fb`](https://github.com/akki-team/sylius-localization-plugin/commit/e5751fb6a4cf27c6542ecf744e968a940a2936ea) - akki:translations:load vide du cache les traductions importées ou effacées *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`2e48346`](https://github.com/akki-team/sylius-localization-plugin/commit/2e4834617951cf5e581caf0e9332bee4ee5a54a2) - DatabaseTranslator résout le canal à chaque appel *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`503b325`](https://github.com/akki-team/sylius-localization-plugin/commit/503b3256d58be03a65106cfb89aed67405df4082) - type nullable explicite sur getValue() (dépréciation PHP 8.4) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`ec6e0e6`](https://github.com/akki-team/sylius-localization-plugin/commit/ec6e0e6169ee80125a2b6927b75d9fa950ca1e0a) - DatabaseTranslator garde le canal jusqu'au prochain kernel.reset *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`4542ebd`](https://github.com/akki-team/sylius-localization-plugin/commit/4542ebdd05e06df2796f2c0e484dc0dca5cb60db) - mémoire de requête bornée et jeton de version plus robuste *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*
- [`80412f4`](https://github.com/akki-team/sylius-localization-plugin/commit/80412f46ad1f10d3fb71b2e5d79b58ca99b49b73) - vider toutes les locales de la boutique, même si l'import échoue *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :zap: Performance Improvements
- [`5e8a40e`](https://github.com/akki-team/sylius-localization-plugin/commit/5e8a40eb438e07c5cf3ce4734938e1946560fe27) - index de recherche sur akki_localization_entry (canal, clé, domaine) *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*

### :white_check_mark: Tests
- [`c216a34`](https://github.com/akki-team/sylius-localization-plugin/commit/c216a34285985e74d96a08ae2b9cf2f4ac82b01b) - tests unitaires du cache et du traducteur *(commit by [@sebastien-akki](https://github.com/sebastien-akki))*


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
