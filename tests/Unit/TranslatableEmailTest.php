<?php

namespace Softspring\Component\MimeTranslatable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Softspring\Component\MimeTranslatable\ExtendedContextEmail;
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

    public function testExtendedContextHelpersStoreAndReadContextBlocksAndParams(): void
    {
        $email = new class extends ExtendedContextEmail {
            public function exposeSetContextBlock(string $key, array $block = []): void
            {
                $this->setContextBlock($key, $block);
            }

            public function exposeGetContextBlock(string $key): array
            {
                return $this->getContextBlock($key);
            }

            public function exposeSetContextParam(string $key, mixed $value): void
            {
                $this->setContextParam($key, $value);
            }

            public function exposeGetContextParam(string $key): mixed
            {
                return $this->getContextParam($key);
            }
        };

        $email->exposeSetContextBlock('buttons', ['confirm' => '#confirm']);
        $email->exposeSetContextParam('token', 'abc123');

        self::assertSame(['confirm' => '#confirm'], $email->exposeGetContextBlock('buttons'));
        self::assertSame([], $email->exposeGetContextBlock('missing'));
        self::assertSame('abc123', $email->exposeGetContextParam('token'));
        self::assertNull($email->exposeGetContextParam('missing'));
        self::assertSame([
            'buttons' => ['confirm' => '#confirm'],
            'token' => 'abc123',
        ], $email->getContext());
    }
}
