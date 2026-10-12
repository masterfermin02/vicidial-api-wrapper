<?php

declare(strict_types=1);

namespace VicidialApi\Parameters;

/**
 * Typed parameters for `Admin::updateLead()` (Non-Agent API function `update_lead`).
 *
 * Match the lead with `leadId`, `vendorLeadCode` or `phoneNumber` (plus `searchMethod`).
 * Set a field to empty with the literal string `--BLANK--`. Booleans are sent as Y / N.
 */
final class UpdateLeadParameters extends Parameters
{
    public function __construct(
        public readonly string|int|null $leadId = null,
        public readonly ?string $vendorLeadCode = null,
        public readonly ?string $phoneNumber = null,
        /** LEAD_ID, VENDOR_LEAD_CODE, PHONE_NUMBER or a combination such as LEAD_ID_VENDOR_LEAD_CODE. */
        public readonly ?string $searchMethod = null,
        /** LIST, CAMPAIGN or SYSTEM. */
        public readonly ?string $searchLocation = null,
        public readonly string|int|null $listId = null,
        public readonly ?bool $insertIfNotFound = null,
        public readonly ?int $records = null,
        public readonly ?bool $customFields = null,
        public readonly ?bool $noUpdate = null,
        public readonly ?bool $deleteLead = null,
        public readonly ?bool $deleteCfData = null,
        public readonly ?bool $resetLead = null,
        /** true, false or 'REMOVE'. */
        public readonly bool|string|null $callback = null,
        public readonly ?string $callbackStatus = null,
        public readonly ?string $callbackDatetime = null,
        public readonly ?string $callbackType = null,
        public readonly ?string $callbackUser = null,
        public readonly ?string $callbackComments = null,
        public readonly ?bool $updatePhoneNumber = null,
        public readonly ?bool $addToHopper = null,
        public readonly ?bool $removeFromHopper = null,
        public readonly ?int $hopperPriority = null,
        public readonly ?bool $hopperLocalCallTimeCheck = null,
        public readonly ?bool $listExistsCheck = null,
        public readonly ?bool $archivedLead = null,
        public readonly ?string $userField = null,
        public readonly string|int|null $listIdField = null,
        public readonly ?string $status = null,
        public readonly ?string $sourceId = null,
        public readonly string|int|null $gmtOffsetNow = null,
        public readonly ?string $title = null,
        public readonly ?string $firstName = null,
        public readonly ?string $middleInitial = null,
        public readonly ?string $lastName = null,
        public readonly ?string $address1 = null,
        public readonly ?string $address2 = null,
        public readonly ?string $address3 = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $province = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $countryCode = null,
        public readonly ?string $gender = null,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $altPhone = null,
        public readonly ?string $email = null,
        public readonly ?string $securityPhrase = null,
        public readonly ?string $comments = null,
        public readonly string|int|null $rank = null,
        public readonly ?string $owner = null,
        public readonly ?int $calledCount = null,
        public readonly string|int|null $phoneCode = null,
        public readonly string|int|null $entryListId = null,
        public readonly string|int|null $forceEntryListId = null,
    ) {}
}
