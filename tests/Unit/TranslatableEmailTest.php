<?php

namespace Softspring\Component\MimeTranslatable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Softspring\Component\MimeTranslatable\TranslatableEmail;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class TranslatableEmailTest extends TestCase
{
    public function testSubjectIsTranslatedWithDomainAndLocale(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [
            'mail.subject' => 'Hello %name%',
        ], 'en', 'messages');

        $email = new TranslatableEmail($translator, 'en');
        $email->setTranslationParams([
            '%name%' => 'Mery',
        ]);
        $email->subject('mail.subject', 'messages');

        $this->assertSame('Hello Mery', $email->getSubject());
    }

    public function testTranslationParamsAreStoredInEmailContext(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        $email = new TranslatableEmail($translator, 'en');
        $email->setTranslationParams([
            '%name%' => 'Mery',
            '%url%' => '#confirm-url',
        ]);

        $this->assertSame([
            '%name%' => 'Mery',
            '%url%' => '#confirm-url',
        ], $email->getTranslationParams());
        $this->assertSame([
            '__translation_params' => [
                '%name%' => 'Mery',
                '%url%' => '#confirm-url',
            ],
        ], $email->getContext());
    }

    public function testLocaleIsExposed(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        $email = new TranslatableEmail($translator, 'es');

        $this->assertSame('es', $email->getLocale());
    }
}
