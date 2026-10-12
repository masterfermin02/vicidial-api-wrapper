<?php

declare(strict_types=1);

namespace VicidialApi\Parameters;

/**
 * Typed parameters for `Agent::dispo()` (Agent API function `external_status`).
 */
final class DispoParameters extends Parameters
{
    protected const KEY_MAP = ['status' => 'value'];

    public function __construct(
        /** Any status defined in VICIdial, for example SALE, NI, CALLBK. Sent as `value`. */
        public readonly string $status,
        /** YYYY-MM-DD HH:MM:SS, required when the status is a scheduled callback. */
        public readonly ?string $callbackDatetime = null,
        /** USERONLY or ANYONE. */
        public readonly ?string $callbackType = null,
        public readonly ?string $callbackComments = null,
        public readonly ?string $qmDispoCode = null,
    ) {}
}
