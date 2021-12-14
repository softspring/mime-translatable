<?php

namespace Softspring\Component\MimeTranslatable;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ExtendedContextEmail extends TemplatedEmail
{
    protected function setContextBlock(string $key, array $block = [])
    {
        $this->context(array_merge($this->getContext(), [$key => $block]));
    }

    protected function getContextBlock(string $key): array
    {
        return $this->getContext()[$key] ?? [];
    }

    protected function setContextParam(string $key, $value)
    {
        $this->context(array_merge($this->getContext(), [$key => $value]));
    }

    protected function getContextParam(string $key)
    {
        return $this->getContext()[$key] ?? null;
    }
}