<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Neo\BasicBundle\License\LicenseService;

/**
 * Refreshes the licence certificates that are due (the licence service asks
 * for a daily check). Runs hourly, does nothing while no check is due.
 */
#[AsCronJob('hourly')]
final class RefreshLicensesCron
{
    public function __construct(private readonly LicenseService $licenses)
    {
    }

    public function __invoke(): void
    {
        $this->licenses->refreshDue();
    }
}
