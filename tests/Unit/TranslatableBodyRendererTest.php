<?php

namespace Softspring\Component\MimeTranslatable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Softspring\Component\MimeTranslatable\TranslatableBodyRenderer;
use Softspring\Component\MimeTranslatable\TranslatableEmail;
use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class TranslatableBodyRendererTest extends TestCase
{
    public function testRenderTemporarilySwitchesTranslatorLocaleForTranslatableEmail(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        $bodyRenderer = new class($translator) implements BodyRendererInterface {
            public string $localeDuringRender = '';

            public function __construct(private readonly Translator $translator)
            {
            }

            public function render(Message $message): void
            {
                $this->localeDuringRender = $this->translator->getLocale();
            }
        };

        $renderer = new TranslatableBodyRenderer($bodyRenderer, $translator);
        $email = new TranslatableEmail($translator, 'es');

        $renderer->render($email);

        $this->assertSame('es', $bodyRenderer->localeDuringRender);
        $this->assertSame('en', $translator->getLocale());
    }

    public function testRenderKeepsTranslatorLocaleForRegularMessages(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        $bodyRenderer = new class($translator) implements BodyRendererInterface {
            public string $localeDuringRender = '';

            public function __construct(private readonly Translator $translator)
            {
            }

            public function render(Message $message): void
            {
                $this->localeDuringRender = $this->translator->getLocale();
            }
        };

        $renderer = new TranslatableBodyRenderer($bodyRenderer, $translator);
        $message = new Message();

        $renderer->render($message);

        $this->assertSame('en', $bodyRenderer->localeDuringRender);
        $this->assertSame('en', $translator->getLocale());
    }

    public function testRenderRestoresTranslatorLocaleAfterFailure(): void
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        $bodyRenderer = new class implements BodyRendererInterface {
            public function render(Message $message): void
            {
                throw new RuntimeException('Rendering failed');
            }
        };

        $renderer = new TranslatableBodyRenderer($bodyRenderer, $translator);
        $email = new TranslatableEmail($translator, 'es');

        try {
            $renderer->render($email);
            self::fail('An exception was expected');
        } catch (RuntimeException $exception) {
            $this->assertSame('Rendering failed', $exception->getMessage());
        }

        $this->assertSame('en', $translator->getLocale());
    }
}
