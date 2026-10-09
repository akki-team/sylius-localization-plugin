<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersion;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Copies locales devant le cache partagé des traductions (CacheMessageProvider).
 *
 * Chaque |trans interroge le cache partagé jusqu'à 2 fois (domaine messages, puis
 * messages+intl-icu), sans mémoire : avec Redis, c'est un aller-retour réseau par texte affiché.
 * Deux niveaux :
 * - mémoire de la requête, vidée par kernel.reset (entre deux messages Messenger) ;
 * - APCu du serveur, en option : ses clés portent le jeton de LocalizationCacheVersion, qui
 *   change à chaque invalidation, et une durée de vie courte borne les invalidations faites
 *   hors du plugin (suppression manuelle d'une entrée du cache partagé).
 */
final class LocalCacheMessageProvider implements MessageProviderInterface, ResetInterface
{
    // Borne pour les commandes longues, dont la mémoire n'est vidée qu'entre deux messages.
    private const MEMO_MAX_ENTRIES = 5000;

    private array $memo = [];

    private ?bool $apcuAvailable = null;

    public function __construct(
        private readonly MessageProviderInterface  $decorated,
        private readonly CacheKeyResolverInterface $cacheKeyResolver,
        private readonly LocalizationCacheVersion  $version,
        private readonly bool                      $apcuEnabled = false,
        private readonly int                       $apcuTtl = 300,
        private readonly string                    $apcuPrefix = 'akki_localization.',
    )
    {
    }

    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
    {
        $key = $this->cacheKeyResolver->getKey($id, $domain, $locale, $channelCode);

        if (true === \array_key_exists($key, $this->memo)) {
            return $this->memo[$key];
        }

        if (\count($this->memo) >= self::MEMO_MAX_ENTRIES) {
            $this->memo = [];
        }

        return $this->memo[$key] = $this->fetch($key, $id, $domain, $locale, $channelCode);
    }

    public function reset(): void
    {
        $this->memo = [];
    }

    private function fetch(string $key, string $id, string $domain, string $locale, string $channelCode): ?string
    {
        if (false === $this->isApcuAvailable()) {
            return $this->decorated->getMessage($id, $domain, $locale, $channelCode);
        }

        $version = $this->version->current();

        if (null === $version) {
            return $this->decorated->getMessage($id, $domain, $locale, $channelCode);
        }

        $apcuKey = $this->apcuPrefix . $version . '.' . $key;
        $message = apcu_fetch($apcuKey, $found);

        if (true === $found) {
            return $message;
        }

        $message = $this->decorated->getMessage($id, $domain, $locale, $channelCode);
        apcu_store($apcuKey, $message, $this->apcuTtl);

        return $message;
    }

    /**
     * En CLI, apc.enable_cli vaut 0 par défaut : apcu_store échouerait sans erreur (et
     * l'ApcuAdapter de Symfony ne le détecte pas, il ne teste que apc.enabled).
     */
    private function isApcuAvailable(): bool
    {
        return $this->apcuAvailable ??= true === $this->apcuEnabled && \function_exists('apcu_enabled') && apcu_enabled();
    }
}
