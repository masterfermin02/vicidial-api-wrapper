<?php

declare(strict_types=1);

namespace VicidialApi\Parameters;

/**
 * Typed parameters for `Agent::dial()` (Agent API function `external_dial`).
 *
 * Either `phoneNumber` or `leadId` is required; when both are given VICIdial uses `leadId`.
 */
final class DialParameters extends Parameters
{
    protected const YES = 'YES';

    protected const NO = 'NO';

    public function __construct(
        public readonly ?string $phoneNumber = null,
        public readonly string|int|null $leadId = null,
        public readonly string|int|null $phoneCode = null,
        /** Search the campaign lists for the number and bring up that lead. */
        public readonly ?bool $search = null,
        /** Preview the lead without dialing. */
        public readonly ?bool $preview = null,
        /** Bring the agent screen to the front. */
        public readonly ?bool $focus = null,
        public readonly ?string $vendorId = null,
        public readonly ?string $dialPrefix = null,
        public readonly ?string $groupAlias = null,
        public readonly ?bool $vtigerCallback = null,
        public readonly ?string $altUser = null,
        /** ALT, ADDR3 or SEARCH when dialing by lead_id. */
        public readonly ?string $altDial = null,
        public readonly ?string $dialIngroup = null,
        public readonly ?string $outboundCid = null,
    ) {}
}
