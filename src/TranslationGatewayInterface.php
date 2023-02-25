<?php declare(strict_types=1);

namespace Sofyco\Translation\Gateway;

interface TranslationGatewayInterface
{
    public function detect(string $text): ?string;

    public function translate(string $text, string $target, ?string $source = null): ?string;
}
