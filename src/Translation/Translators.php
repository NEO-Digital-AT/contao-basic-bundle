<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Translation;

use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Ai\AiConnection;

/**
 * The translation services an extension can offer: Azure Translator, DeepL
 * and a language model of the AI connection. An extension that lets the user
 * choose stores the code of the service (and, for the language model, the
 * model) and takes the translator from here.
 */
final class Translators
{
    public const AZURE = 'azure';

    public const DEEPL = 'deepl';

    public const AI = 'ai';

    /**
     * All services, in the order in which an extension offers them.
     */
    public const SERVICES = [self::AZURE, self::DEEPL, self::AI];

    public function __construct(
        private readonly AzureTranslator $azure,
        private readonly DeepLTranslator $deepl,
        private readonly AiTranslator $ai,
        private readonly AiConnection $aiConnection,
    ) {
    }

    /**
     * The translator of a service; for the language model with the given model.
     *
     * @throws \InvalidArgumentException for an unknown service
     */
    public function get(string $service, string $model = ''): TranslatorInterface
    {
        return match ($service) {
            self::AZURE => $this->azure,
            self::DEEPL => $this->deepl,
            self::AI => $this->ai->withModel($model),
            default => throw new \InvalidArgumentException(\sprintf('Unknown translation service "%s".', $service)),
        };
    }

    /**
     * Whether the access of a service is set up on "Access keys and licences".
     * For the language model this is the AI connection; the model is chosen by
     * the extension.
     */
    public function hasAccess(string $service): bool
    {
        return match ($service) {
            self::AZURE => $this->azure->isConfigured(),
            self::DEEPL => $this->deepl->isConfigured(),
            self::AI => $this->aiConnection->isConfigured(),
            default => false,
        };
    }

    /**
     * The services whose access is set up, in the order of SERVICES.
     *
     * @return list<string>
     */
    public function withAccess(): array
    {
        return array_values(array_filter(self::SERVICES, $this->hasAccess(...)));
    }

    /**
     * The section of "Access keys and licences" where the access of a service is entered.
     */
    public static function legend(string $service): string
    {
        return match ($service) {
            self::DEEPL => AccessPage::DEEPL_LEGEND,
            self::AI => AccessPage::AI_LEGEND,
            default => AccessPage::AZURE_TRANSLATOR_LEGEND,
        };
    }
}
