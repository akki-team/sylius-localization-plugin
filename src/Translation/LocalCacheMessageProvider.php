<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Translation;

use Akki\SyliusLocalizationPlugin\Cache\LocalizationCacheVersionInterface;
use Akki\SyliusLocalizationPlugin\Cache\Resolver\CacheKeyResolverInterface;
use Symfony\Contracts\Service\ResetInterface;

final class LocalCacheMessageProvider implements MessageProviderInterface, ResetInterface
{
    private const MEMO_MAX_ENTRIES = 5000;

    private const MEMO_MAX_AGE = 10;

    private array $memo = [];

    private int $memoStartedAt = 0;

    private int $memoGeneration = 0;

    private ?bool $apcuAvailable = null;

    public function __construct(
        private readonly MessageProviderInterface          $decorated,
        private readonly CacheKeyResolverInterface         $cacheKeyResolver,
        private readonly LocalizationCacheVersionInterface $version,
        private readonly bool                              $apcuEnabled = false,
        private readonly int                               $apcuTtl = 300,
        private readonly string                            $apcuPrefix = 'akki_localization.',
    )
    {
    }

    public function getMessage(string $id, string $domain, string $locale, string $channelCode): ?string
    {
        $key = $this->cacheKeyResolver->getKey($id, $domain, $locale, $channelCode);

        if ([] !== $this->memo && true === $this->isMemoStale()) {
            $this->memo = [];
        }

        if (true === \array_key_exists($key, $this->memo)) {
            return $this->memo[$key];
        }

        if ([] === $this->memo) {
            $this->memoStartedAt = time();
            $this->memoGeneration = $this->version->getGeneration();
        }

        return $this->memo[$key] = $this->fetch($key, $id, $domain, $locale, $channelCode);
    }

    public function reset(): void
    {
        $this->memo = [];
    }

    private function isMemoStale(): bool
    {
        return \count($this->memo) >= self::MEMO_MAX_ENTRIES
            || $this->memoGeneration !== $this->version->getGeneration()
            || time() - $this->memoStartedAt >= self::MEMO_MAX_AGE;
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

    // apc.enable_cli vaut 0 par défaut : en CLI, apcu_store échoue sans erreur.
    private function isApcuAvailable(): bool
    {
        return $this->apcuAvailable ??= true === $this->apcuEnabled && \function_exists('apcu_enabled') && apcu_enabled();
    }
}
