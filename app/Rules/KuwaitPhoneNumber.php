<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KuwaitPhoneNumber implements ValidationRule
{
    /**
     * Mobile number prefixes according to Kuwait's numbering plan
     */
    private const MOBILE_PREFIXES = ['4', '5', '6', '9'];

    /**
     * Landline number prefix
     */
    private const LANDLINE_PREFIX = '2';

    /**
     * Kuwait country code
     */
    private const COUNTRY_CODE = '965';

    /**
     * International prefixes that should be stripped
     */
    private const INTERNATIONAL_PREFIXES = [
        '+965',
        '00965',
        '011965', // US/Canada international prefix
        '965'
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalizedNumber = $this->normalizePhoneNumber((string) $value);

        if ($normalizedNumber === null) {
            $fail(__($this->getErrorMessage('invalid_format')));
            return;
        }

        if (!$this->isValidKuwaitNumber($normalizedNumber)) {
            $fail(__($this->getErrorMessage($this->getNumberType($normalizedNumber))));
            return;
        }
    }

    /**
     * Normalize phone number by removing non-digits and international prefixes
     */
    private function normalizePhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-digit characters except +
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);

        if (empty($cleaned)) {
            return null;
        }

        // Remove international prefixes
        foreach (self::INTERNATIONAL_PREFIXES as $prefix) {
            if (str_starts_with($cleaned, $prefix)) {
                $cleaned = substr($cleaned, strlen($prefix));
                break;
            }
        }

        // Final cleanup - remove any remaining non-digits
        $cleaned = preg_replace('/[^\d]/', '', $cleaned);

        return empty($cleaned) ? null : $cleaned;
    }

    /**
     * Validate if the normalized number is a valid Kuwaiti number
     */
    private function isValidKuwaitNumber(string $number): bool
    {
        // All Kuwaiti numbers are 8 digits after 2008 numbering plan
        if (strlen($number) !== 8) {
            return false;
        }

        $firstDigit = $number[0];

        // Mobile numbers: start with 4, 5, 6, or 9
        if (in_array($firstDigit, self::MOBILE_PREFIXES)) {
            return $this->isValidMobileNumber($number);
        }

        // Landline numbers: start with 2
        if ($firstDigit === self::LANDLINE_PREFIX) {
            return $this->isValidLandlineNumber($number);
        }

        return false;
    }

    /**
     * Validate mobile number pattern
     */
    private function isValidMobileNumber(string $number): bool
    {
        // Mobile: 8 digits starting with 4, 5, 6, or 9
        return preg_match('/^[4569]\d{7}$/', $number) === 1;
    }

    /**
     * Validate landline number pattern  
     */
    private function isValidLandlineNumber(string $number): bool
    {
        // Landline: 8 digits starting with 2
        return preg_match('/^2\d{7}$/', $number) === 1;
    }

    /**
     * Determine the type of number for error messaging
     */
    private function getNumberType(string $number): string
    {
        if (empty($number)) {
            return 'invalid_format';
        }

        $length = strlen($number);
        $firstDigit = $number[0] ?? '';

        if ($length !== 8) {
            return 'invalid_length';
        }

        if (in_array($firstDigit, self::MOBILE_PREFIXES)) {
            return 'invalid_mobile';
        }

        if ($firstDigit === self::LANDLINE_PREFIX) {
            return 'invalid_landline';
        }

        return 'invalid_prefix';
    }

    /**
     * Get appropriate error message based on validation failure type
     */
    private function getErrorMessage(string $type): string
    {
        return match ($type) {
            'invalid_mobile' => 'validation.kuwait_phone_number.invalid_mobile',
            'invalid_landline' => 'validation.kuwait_phone_number.invalid_landline',
            'invalid_length' => 'validation.kuwait_phone_number.invalid_length',
            'invalid_prefix' => 'validation.kuwait_phone_number.invalid_prefix',
            default => 'validation.kuwait_phone_number.default',
        };
    }


    /**
     * Convert normalized number to E.164 format
     * This method can be used externally for storing numbers in international format
     */
    public static function toE164Format(string $phoneNumber): ?string
    {
        $validator = new self();
        $normalized = $validator->normalizePhoneNumber($phoneNumber);

        if ($normalized === null || !$validator->isValidKuwaitNumber($normalized)) {
            return null;
        }

        return '+' . self::COUNTRY_CODE . $normalized;
    }

    /**
     * Format number for display (Kuwait local format)
     */
    public static function formatForDisplay(string $phoneNumber): ?string
    {
        $validator = new self();
        $normalized = $validator->normalizePhoneNumber($phoneNumber);

        if ($normalized === null || !$validator->isValidKuwaitNumber($normalized)) {
            return null;
        }

        // Format as XXXX XXXX for better readability
        return substr($normalized, 0, 4) . ' ' . substr($normalized, 4);
    }
}
