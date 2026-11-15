<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

/**
 * Why a translation could not be made. The reason is one of the constants, so
 * an extension can show its own text for it; $service names the translation
 * service ("Azure Translator", "DeepL", "AI model").
 */
final class TranslationException extends \RuntimeException
{
    public const NOT_CONFIGURED = 'not_configured';

    public const UNREACHABLE = 'unreachable';

    public const REJECTED = 'rejected';

    private const AZURE = 'Azure Translator';

    private function __construct(
        public readonly string $reason,
        string $message,
        public readonly int $status = 0,
        public readonly string $service = self::AZURE,
    ) {
        parent::__construct($message);
    }

    public static function notConfigured(): self
    {
        return self::notConfiguredFor(self::AZURE);
    }

    public static function unreachable(): self
    {
        return self::unreachableFor(self::AZURE);
    }

    public static function rejected(int $status): self
    {
        return self::rejectedBy(self::AZURE, $status);
    }

    public static function notConfiguredFor(string $service): self
    {
        return new self(self::NOT_CONFIGURED, \sprintf('%s is not configured.', $service), 0, $service);
    }

    public static function unreachableFor(string $service): self
    {
        return new self(self::UNREACHABLE, \sprintf('%s cannot be reached.', $service), 0, $service);
    }

    /**
     * The service answered, but with an error or without a usable translation ($status is its HTTP status).
     */
    public static function rejectedBy(string $service, int $status): self
    {
        return new self(self::REJECTED, \sprintf('%s answered HTTP %d without a usable translation.', $service, $status), $status, $service);
    }
}
