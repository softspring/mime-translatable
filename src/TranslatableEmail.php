<?php

namespace Softspring\Component\MimeTranslatable;

use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatableEmail extends ExtendedContextEmail
{
    protected ?string $locale;

    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, ?string $locale = null, ?Headers $headers = null, ?AbstractPart $body = null)
    {
        parent::__construct($headers, $body);
        $this->translator = $translator;
        $this->locale = $locale;
    }

    public function subject(string $subject, ?string $domain = null): static
    {
        $subject = $this->translator->trans($subject, $this->getTranslationParams(), $domain, $this->locale);

        return parent::subject($subject);
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setTranslationParams(array $context): self
    {
        $this->setContextBlock('__translation_params', $context);

        return $this;
    }

    public function getTranslationParams(): array
    {
        return $this->getContextBlock('__translation_params');
    }
}
