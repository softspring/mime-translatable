<?php

namespace Softspring\Component\MimeTranslatable;

use Symfony\Component\Mime\BodyRendererInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Translation\Translator;

class TranslatableBodyRenderer implements BodyRendererInterface
{
    protected BodyRendererInterface $bodyRenderer;

    protected Translator $translator;

    public function __construct(BodyRendererInterface $bodyRenderer, Translator $translator)
    {
        $this->bodyRenderer = $bodyRenderer;
        $this->translator = $translator;
    }

    public function render(Message $message): void
    {
        if ($message instanceof TranslatableEmail && $message->getLocale()) {
            $storedLocale = $this->translator->getLocale();
            $this->translator->setLocale($message->getLocale());
        }

        $this->bodyRenderer->render($message);

        if (isset($storedLocale)) {
            $this->translator->setLocale($storedLocale);
        }
    }
}