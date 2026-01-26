<?php

declare(strict_types=1);

namespace VicidialApi\Responses;

use JsonException;
use Stringable;
use VicidialApi\Contracts\ResponseContract;

/**
 * Vicidial API Response DTO
 *
 * Provides structured access to API responses with helpers for common operations.
 *
 * @implements ResponseContract<array{status: string, raw: string, parsed: mixed, requestId: string|null}>
 */
final class VicidialResponse implements ResponseContract, Stringable
{
    /** @use Concerns\ArrayAccessible<array<string, mixed>> */
    use Concerns\ArrayAccessible;

    /**
     * @param  array<string, mixed>  $parsed
     */
    private function __construct(
        private readonly string $status,
        private readonly string $raw,
        private readonly array $parsed,
        private readonly ?string $requestId = null,
    ) {}

    /**
     * Create a VicidialResponse from raw API response content.
     *
     * @param  array<string, string>  $headers
     */
    public static function from(string $content, array $headers = []): self
    {
        $raw = $content;
        $parsed = self::parseContent($content);
        $status = self::determineStatus($content, $parsed);
        $requestId = $headers['X-Request-ID'] ?? $headers['x-request-id'] ?? null;

        return new self($status, $raw, $parsed, $requestId);
    }

    /**
     * Get the response status.
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Get the raw response string.
     */
    public function raw(): string
    {
        return $this->raw;
    }

    /**
     * Get the parsed response as an array.
     *
     * @return array<string, mixed>
     */
    public function parsed(): array
    {
        return $this->parsed;
    }

    /**
     * Get the request ID if available.
     */
    public function requestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * Check if the response indicates success.
     * Vicidial returns "SUCCESS:" prefix for successful operations
     */
    public function ok(): bool
    {
        // Check status first (most reliable)
        if ($this->status === 'success') {
            return true;
        }

        // Additional check for explicit SUCCESS prefix
        return str_starts_with(strtoupper($this->raw), 'SUCCESS:');
    }

    /**
     * Check if the response indicates an error.
     */
    public function failed(): bool
    {
        return ! $this->ok();
    }

    /**
     * Get the response as an array.
     *
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        return $this->parsed;
    }

    /**
     * Get the response as text.
     */
    public function asText(): string
    {
        return $this->raw;
    }

    /**
     * Get a specific value from the parsed response.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parsed[$key] ?? $default;
    }

    /**
     * Check if a key exists in the parsed response.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parsed);
    }

    /**
     * Get the response as a JSON string.
     *
     * @throws JsonException
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | $flags);
    }

    /**
     * {@inheritDoc}
     *
     * @return array{status: string, raw: string, parsed: array<string, mixed>, requestId: string|null}
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'raw' => $this->raw,
            'parsed' => $this->parsed,
            'requestId' => $this->requestId,
        ];
    }

    /**
     * Convert the response to a string (returns raw content).
     */
    public function __toString(): string
    {
        return $this->raw;
    }

    /**
     * Parse the content into a structured array.
     * Based on actual Vicidial API response formats from documentation.
     *
     * @return array<string, mixed>
     */
    private static function parseContent(string $content): array
    {
        $content = trim($content);

        // Vicidial typically returns pipe-delimited responses
        // Examples:
        // - "SUCCESS: external_pause function set - PAUSE|1232020456|6666"
        // - "ERROR: agent_user is not logged in - 6666"
        // - "VERSION: 2.0.5-2|BUILD: 90116-1229|DATE: 2009-01-15 14:59:33|EPOCH: 1222020803"

        $parsed = [];

        // Check if response starts with SUCCESS or ERROR
        if (preg_match('/^(SUCCESS|ERROR|NOTICE):\s*(.+)$/i', $content, $matches)) {
            $type = strtoupper($matches[1]);
            $message = $matches[2];

            $parsed['result_type'] = $type;
            $parsed['message'] = $message;

            // Parse the message for additional pipe-delimited data
            if (str_contains($message, '|')) {
                $parts = explode('|', $message);
                foreach ($parts as $index => $part) {
                    $parsed["data_$index"] = trim($part);
                }
            }

            return $parsed;
        }

        // Check for multi-line key-value format (like webserver response)
        // Example: "set.timezone: America/New_York\nabbr.timezone: EDT"
        if (str_contains($content, "\n") && str_contains($content, ':')) {
            $lines = explode("\n", $content);
            $hasKeyValue = false;

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, ';')) {
                    continue;
                }

                if (preg_match('/^([^:]+):\s*(.*)$/', $line, $matches)) {
                    $key = trim($matches[1]);
                    $value = trim($matches[2]);
                    $parsed[$key] = $value;
                    $hasKeyValue = true;
                }
            }

            if ($hasKeyValue) {
                return $parsed;
            }
        }

        // Parse pipe-delimited data (common for lists and version info)
        // Example: "user|allowed_campaigns|allowed_ingroups"
        if (str_contains($content, '|')) {
            $parts = explode('|', $content);

            // Check if it looks like VERSION format
            if (str_starts_with($content, 'VERSION:')) {
                foreach ($parts as $part) {
                    if (preg_match('/^([A-Z]+):\s*(.+)$/', trim($part), $matches)) {
                        $parsed[strtolower($matches[1])] = trim($matches[2]);
                    }
                }

                if ($parsed !== []) {
                    return $parsed;
                }
            }

            // Generic pipe-delimited data
            foreach ($parts as $index => $value) {
                $parsed["field_$index"] = trim($value);
            }

            return $parsed;
        }

        // Parse CSV-like format with headers (some reports use this)
        if (str_contains($content, ',') && ! str_contains($content, "\n")) {
            $parts = str_getcsv($content);
            foreach ($parts as $index => $value) {
                if ($value) {
                    $parsed["field_$index"] = trim($value);
                }
            }

            return $parsed;
        }

        // Try JSON parsing (rare but possible with some custom implementations)
        if (self::isJson($content)) {
            try {
                /** @var array<string, mixed> $decoded */
                $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                return $decoded;
            } catch (JsonException) {
                // Fall through
            }
        }

        // Return raw content as single result
        return ['result' => $content];
    }

    /**
     * Determine the status from the content.
     * Vicidial responses typically start with SUCCESS:, ERROR:, or NOTICE:
     *
     * @param  array<string, mixed>  $parsed
     */
    private static function determineStatus(string $content, array $parsed): string
    {
        $contentUpper = strtoupper($content);

        // Check for explicit SUCCESS indicator (most common Vicidial format)
        if (str_starts_with($contentUpper, 'SUCCESS:')) {
            return 'success';
        }

        // Check for explicit ERROR indicator
        if (str_starts_with($contentUpper, 'ERROR:')) {
            return 'error';
        }

        // Check for NOTICE indicator (usually warnings)
        if (str_starts_with($contentUpper, 'NOTICE:')) {
            return 'notice';
        }

        // Check parsed data for result_type
        if (isset($parsed['result_type']) && is_string($parsed['result_type'])) {
            $resultType = strtolower($parsed['result_type']);
            if ($resultType === 'success') {
                return 'success';
            }
            if ($resultType === 'error') {
                return 'error';
            }
            if ($resultType === 'notice') {
                return 'notice';
            }
        }

        // For responses that don't start with SUCCESS/ERROR (like version, lists, etc.)
        // we consider them successful if they don't contain ERROR
        if (str_contains($contentUpper, 'ERROR')) {
            return 'error';
        }

        // Default to success for informational responses (version, lists, etc.)
        return 'success';
    }

    /**
     * Check if a string is valid JSON.
     */
    private static function isJson(string $string): bool
    {
        if (! str_starts_with(trim($string), '{') && ! str_starts_with(trim($string), '[')) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
