<?php
declare(strict_types=1);


namespace Akki\SyliusLocalizationPlugin\Tests\Unit\Cache;

use Akki\SyliusLocalizationPlugin\Cache\CacheKeySanitizer;
use PHPUnit\Framework\TestCase;

final class CacheKeySanitizerTest extends TestCase
{
    public function testReservedCharactersAreReplaced(): void
    {
        self::assertSame(
            'fls.fr_FR.validators.This value should be of type __ type __.',
            (new CacheKeySanitizer())->sanitize('fls.fr_FR.validators.This value should be of type {{ type }}.'),
        );
        self::assertSame('a_b_c_d_e_f_g_h', (new CacheKeySanitizer())->sanitize('a{b}c(d)e/f\\g@h'));
        self::assertSame('a_b', (new CacheKeySanitizer())->sanitize('a:b'));
    }

    public function testPlainKeyIsUnchanged(): void
    {
        self::assertSame('fls.fr_FR.messages.app.ui.title', (new CacheKeySanitizer())->sanitize('fls.fr_FR.messages.app.ui.title'));
    }
}
