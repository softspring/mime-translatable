# Mime Translatable Features

Functional definition for `softspring/mime-translatable`.

This file defines the expected behavior and functional scope of the component.

## Purpose

- Provide a translation-aware email class on top of Symfony Mime and Twig Bridge.
- Make translated subjects and translated email body rendering easier to use in application mail classes.
- Support previewable example emails for admin tooling.

## Main Features

- Provide `TranslatableEmail` as an extension of Symfony `TemplatedEmail`.
- Translate the subject through Symfony Translation when `subject()` is called.
- Allow translation parameters to be stored in the email context.
- Preserve extra email context values through `ExtendedContextEmail`.
- Provide `TranslatableBodyRenderer` to render emails with the message locale applied temporarily to the translator.
- Provide `ExampleEmailInterface` for email classes that can generate previewable example messages.

## Email Behavior Expectations

- A translatable email should accept a translator and an optional locale.
- The email subject should be translated using the current translation params and optional domain.
- Translation params should be reusable from Twig templates through the email context.
- The configured locale should be available to body rendering.

## Rendering Expectations

- When a `TranslatableEmail` has a locale, the body renderer should switch the translator locale only for that render.
- After rendering, the original translator locale should be restored.
- Non-translatable Symfony messages should still be rendered normally.

## Integration Expectations

- Application email classes should be able to extend `TranslatableEmail`.
- Application email classes should be able to implement `ExampleEmailInterface` to support previews and test sends.
- Other bundles should be able to decorate Symfony's body renderer with `TranslatableBodyRenderer`.

## Current Limits

- Subject translation happens when `subject()` is called, not lazily during final rendering.
- The component does not register services by itself; applications or bundles must wire the renderer where needed.
- The example email interface is only a contract; preview workflows are implemented by higher-level bundles such as `mailer-bundle`.
