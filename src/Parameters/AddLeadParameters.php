<?php

declare(strict_types=1);

namespace VicidialApi\Parameters;

/**
 * Typed parameters for `Admin::addLead()` (Non-Agent API function `add_lead`).
 *
 * Field names and limits follow NON-AGENT_API.txt. Booleans are sent as Y / N.
 * Custom list fields can be added with `with('field_label', 'value')` together with `customFields: true`.
 */
final class AddLeadParameters extends Parameters
{
    public function __construct(
        /** 6-16 digits. */
        public readonly string $phoneNumber,
        /** 1-4 digits, VICIdial defaults to 1. */
        public readonly string|int|null $phoneCode = null,
        /** 3-12 digits, VICIdial defaults to 999. */
        public readonly string|int|null $listId = null,
        /** true, false or 'AREACODE'. */
        public readonly bool|string|null $dncCheck = null,
        /** true, false or 'AREACODE'. */
        public readonly bool|string|null $campaignDncCheck = null,
        public readonly ?string $campaignId = null,
        public readonly ?bool $addToHopper = null,
        /** 99 to -99. */
        public readonly ?int $hopperPriority = null,
        public readonly ?bool $hopperLocalCallTimeCheck = null,
        /** For example DUPLIST, DUPCAMP, DUPSYS90DAY; several can be joined with '-'. */
        public readonly ?string $duplicateCheck = null,
        public readonly ?bool $usacanPrefixCheck = null,
        public readonly ?bool $usacanAreacodeCheck = null,
        public readonly ?bool $nanpaAcPrefixCheck = null,
        public readonly ?bool $customFields = null,
        /** POSTAL, TZCODE or NANPA. */
        public readonly ?string $tzMethod = null,
        public readonly ?bool $callback = null,
        public readonly ?string $callbackStatus = null,
        /** YYYY-MM-DD HH:MM:SS, NOW or xDAYS. */
        public readonly ?string $callbackDatetime = null,
        /** USERONLY or ANYONE. */
        public readonly ?string $callbackType = null,
        public readonly ?string $callbackUser = null,
        public readonly ?string $callbackComments = null,
        public readonly ?bool $lookupState = null,
        public readonly ?bool $listExistsCheck = null,
        public readonly ?string $vendorLeadCode = null,
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
        /** U, M or F. */
        public readonly ?string $gender = null,
        /** YYYY-MM-DD. */
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $altPhone = null,
        public readonly ?string $email = null,
        public readonly ?string $securityPhrase = null,
        public readonly ?string $comments = null,
        /** number_code_note!number_code_note, see the official doc. */
        public readonly ?string $multiAltPhones = null,
        public readonly string|int|null $rank = null,
        public readonly ?string $owner = null,
        public readonly string|int|null $entryListId = null,
    ) {}
}
