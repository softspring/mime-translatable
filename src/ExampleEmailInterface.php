<?php

namespace Softspring\Component\MimeTranslatable;

use Symfony\Contracts\Translation\TranslatorInterface;

interface ExampleEmailInterface
{
    public static function generateExample(TranslatorInterface $translator, ?string $locale = null): TranslatableEmail;
}
