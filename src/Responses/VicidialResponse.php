<?php

declare(strict_types=1);

namespace VicidialApi\Responses;

use JsonException;
use Stringable;
use VicidialApi\Contracts\ResponseContract;

/**
 * Vicidial API Response DTO.
 *
 * Wraps the plain-text body VICIdial returns and exposes it three ways:
 *
 * - `raw()` the body as received
 * - `get()` / `parsed()` a key => value view (see docs/response-formats.md for the keys per format)
 * - `fields()` / `rows()` positional data for pipe / CSV / tab payloads
 *
 * Status is `success`, `error` or `notice`. Empty bodies, HTML bodies (login pages,
 * proxy error pages, detected from the body itself) and HTTP status codes of 400 and
 * above are reported as `error` even though VICIdial did not say `ERROR:`.
 *
 * @implements ResponseContract<array<string, mixed>>
 */
final class VicidialResponse implements ResponseContract, Stringable
{
    /** @use Concerns\ArrayAccessible<array<string, mixed>> */
    use Concerns\ArrayAccessible;

    public const STATUS_SUCCESS = 'success';

    public const STATUS_ERROR = 'error';

    public const STATUS_NOTICE = 'notice';

    /**
     * @param  array<string, mixed>  $parsed
     * @param  array<string, string>  $headers
     */
    private function __construct(
        private readonly string $status,
        private readonly string $raw,
        private readonly array $parsed,
        private readonly ?string $requestId = null,
        private readonly ?int $statusCode = null,
        private readonly array $headers = [],
    ) {}

    /**
     * Create a VicidialResponse from raw API response content.
     *
     * @param  string  $content  response body
     * @param  array<string, string>  $headers  response headers (first value per header)
     * @param  int|null  $statusCode  HTTP status code when known
     * @param  bool|null  $hasHeader  whether a tabular body carries a header row (`header=YES` was sent); null = detect
     */
    public static function from(string $content, array $headers = [], ?int $statusCode = null, ?bool $hasHeader = null): self
    {
        $requestId = self::headerValue($headers, 'X-Request-ID');
        $contentType = strtolower(self::headerValue($headers, 'Content-Type') ?? '');

        if (trim($content) === '') {
            return new self(self::STATUS_ERROR, $content, ['message' => 'Empty response body'], $requestId, $statusCode, $headers);
        }

        $parsed = self::parseContent($content, $hasHeader);

        // An HTML body is a login page, a proxy error page or a web-server error page, never
        // API output. The body decides: VICIdial's scripts may send a text/html Content-Type
        // for perfectly good plain-text answers, so the header only counts when the body is
        // not a shape we recognise.
        $unrecognised = array_keys($parsed) === ['result'];
        if (self::looksLikeHtml($content) || ($unrecognised && str_contains($contentType, 'text/html'))) {
            return new self(
                self::STATUS_ERROR,
                $content,
                ['message' => 'HTML response instead of API output (login page, proxy or web server error?)', 'result' => $content],
                $requestId,
                $statusCode,
                $headers,
            );
        }

        $status = self::determineStatus($content, $statusCode);

        return new self($status, $content, $parsed, $requestId, $statusCode, $headers);
    }

    /**
     * Get the response status: success, error or notice.
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
     * HTTP status code of the response, when the transport provided it.
     */
    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Response headers (first value per header), as provided by the transport.
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * One response header, looked up case-insensitively.
     */
    public function header(string $name): ?string
    {
        return self::headerValue($this->headers, $name);
    }

    /**
     * Check if the response indicates success.
     * Vicidial returns "SUCCESS:" prefix for successful operations
     */
    public function ok(): bool
    {
        if ($this->status === self::STATUS_SUCCESS) {
            return true;
        }

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
     * Message text: everything after `SUCCESS:` / `ERROR:` / `NOTICE:`, or the
     * explanation for empty / HTML bodies. Null for raw data payloads.
     */
    public function message(): ?string
    {
        $message = $this->parsed['message'] ?? null;

        return is_string($message) ? $message : null;
    }

    /**
     * Positional values of a single-line payload, in order.
     *
     * For `SUCCESS: external_pause function set - PAUSE|1232020456|6666` this is
     * ["PAUSE", "1232020456", "6666"] (the part after " - "); for a raw `a|b|c` line
     * it is ["a", "b", "c"] (the `field_N` values). Empty for multi-line payloads: use rows().
     *
     * @return list<string>
     */
    public function fields(): array
    {
        if (isset($this->parsed['result_type'])) {
            $message = $this->message() ?? '';

            if (! str_contains($message, '|')) {
                return [];
            }

            $separator = strpos($message, ' - ');
            $data = $separator === false ? $message : substr($message, $separator + 3);

            return array_map('trim', explode('|', $data));
        }

        $fields = [];
        for ($i = 0; array_key_exists('field_'.$i, $this->parsed); $i++) {
            $value = $this->parsed['field_'.$i];
            $fields[] = is_scalar($value) ? (string) $value : '';
        }

        return $fields;
    }

    /**
     * Rows of a multi-line pipe / CSV / tab payload (reports, lists, `stage=csv|tab|pipe`).
     *
     * With a header row (`header=YES`, or detected) each row is keyed by column name,
     * otherwise rows are lists. Empty for single-line payloads: use fields().
     *
     * @return list<array<int|string, string>>
     */
    public function rows(): array
    {
        /** @var list<array<int|string, string>> $rows */
        $rows = is_array($this->parsed['rows'] ?? null) ? $this->parsed['rows'] : [];

        return $rows;
    }

    /**
     * Column names of a tabular payload when a header row was present.
     *
     * @return list<string>
     */
    public function columns(): array
    {
        /** @var list<string> $columns */
        $columns = is_array($this->parsed['columns'] ?? null) ? $this->parsed['columns'] : [];

        return $columns;
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
     * @return array{status: string, raw: string, parsed: array<string, mixed>, requestId: string|null, statusCode: int|null, headers: array<string, string>}
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'raw' => $this->raw,
            'parsed' => $this->parsed,
            'requestId' => $this->requestId,
            'statusCode' => $this->statusCode,
            'headers' => $this->headers,
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
     *
     * Order matters: a `SUCCESS:` line can contain pipes, JSON contains colons and
     * newlines, and a CSV report can contain `key: value`-looking timestamps.
     *
     * @return array<string, mixed>
     */
    private static function parseContent(string $content, ?bool $hasHeader): array
    {
        $content = trim($content);

        // 1. "SUCCESS: external_pause function set - PAUSE|1232020456|6666"
        if (preg_match('/^(SUCCESS|ERROR|NOTICE):\s*(.*)$/is', $content, $matches)) {
            $type = strtoupper($matches[1]);
            $message = trim($matches[2]);

            $parsed = ['result_type' => $type, 'message' => $message];

            // data_N splits the whole message on "|" (kept for backward compatibility, so
            // data_0 is "external_pause function set - PAUSE"); fields() gives the clean values.
            if (str_contains($message, '|')) {
                foreach (explode('|', $message) as $index => $part) {
                    $parsed["data_$index"] = trim($part);
                }
            }

            return $parsed;
        }

        // 2. JSON (stage=json)
        if (str_starts_with($content, '{') || str_starts_with($content, '[')) {
            try {
                $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($decoded)) {
                    if (array_is_list($decoded)) {
                        /** @var list<array<int|string, string>> $decoded */
                        return ['rows' => $decoded];
                    }

                    /** @var array<string, mixed> $decoded */
                    return $decoded;
                }
            } catch (JsonException) {
                // not JSON after all, fall through
            }
        }

        $lines = array_values(array_filter(array_map('trim', explode("\n", $content)), fn (string $line): bool => $line !== ''));

        // 3. Multi-line tabular data (reports and lists: stage=pipe|csv|tab, optional header=YES)
        if (count($lines) > 1) {
            $table = self::parseTable($lines, $hasHeader);
            if ($table !== null) {
                return $table;
            }
        }

        // 4. Multi-line "key: value" (webserver)
        if (count($lines) > 1) {
            $parsed = [];
            foreach ($lines as $line) {
                if (str_starts_with($line, ';')) {
                    continue;
                }

                if (preg_match('/^([^:]+):\s*(.*)$/', $line, $matches)) {
                    $parsed[trim($matches[1])] = trim($matches[2]);
                }
            }

            if ($parsed !== []) {
                return $parsed;
            }
        }

        // 5. "VERSION: 2.14-890a|BUILD: 210303-1234|DATE: ...|EPOCH: ..."
        if (str_starts_with($content, 'VERSION:') && str_contains($content, '|')) {
            $parsed = [];
            foreach (explode('|', $content) as $part) {
                if (preg_match('/^([A-Z]+):\s*(.+)$/', trim($part), $matches)) {
                    $parsed[strtolower($matches[1])] = trim($matches[2]);
                }
            }

            if ($parsed !== []) {
                return $parsed;
            }
        }

        // 6. Single pipe-delimited line: "user|allowed_campaigns|allowed_ingroups"
        if (str_contains($content, '|')) {
            $parsed = [];
            foreach (explode('|', $content) as $index => $value) {
                $parsed["field_$index"] = trim($value);
            }

            return $parsed;
        }

        // 7. Single CSV line
        if (str_contains($content, ',') && count($lines) === 1) {
            $parsed = [];
            foreach (str_getcsv($content, ',', '"', '\\') as $index => $value) {
                if ($value !== null && $value !== '') {
                    $parsed["field_$index"] = trim($value);
                }
            }

            return $parsed;
        }

        return ['result' => $content];
    }

    /**
     * Detect a delimiter every line shares and split into rows.
     *
     * @param  list<string>  $lines
     * @return array<string, mixed>|null
     */
    private static function parseTable(array $lines, ?bool $hasHeader): ?array
    {
        foreach (["\t", '|', ','] as $delimiter) {
            $count = substr_count($lines[0], $delimiter);
            if ($count === 0) {
                continue;
            }

            foreach ($lines as $line) {
                if (substr_count($line, $delimiter) !== $count) {
                    continue 2;
                }
            }

            $rows = array_map(
                fn (string $line): array => array_map('trim', $delimiter === ','
                    ? array_map(fn (?string $cell): string => (string) $cell, str_getcsv($line, ',', '"', '\\'))
                    : explode($delimiter, $line)),
                $lines,
            );

            $hasHeader ??= self::looksLikeHeader($rows[0]);

            if (! $hasHeader) {
                return ['rows' => $rows, 'delimiter' => $delimiter];
            }

            /** @var list<string> $columns */
            $columns = array_shift($rows);
            $keyed = array_map(fn (array $row): array => array_combine($columns, $row), $rows);

            return ['rows' => $keyed, 'columns' => $columns, 'delimiter' => $delimiter];
        }

        return null;
    }

    /**
     * VICIdial header rows are lowercase identifiers: status,call_id,lead_id,...
     *
     * @param  list<string>  $cells
     */
    private static function looksLikeHeader(array $cells): bool
    {
        foreach ($cells as $cell) {
            if (preg_match('/^[a-z][a-z0-9_]*$/', $cell) !== 1) {
                return false;
            }
        }

        return count(array_unique($cells)) === count($cells);
    }

    /**
     * Determine the status from the content.
     */
    private static function determineStatus(string $content, ?int $statusCode): string
    {
        $contentUpper = strtoupper(ltrim($content));

        if (str_starts_with($contentUpper, 'SUCCESS:')) {
            return self::STATUS_SUCCESS;
        }

        if (str_starts_with($contentUpper, 'ERROR:')) {
            return self::STATUS_ERROR;
        }

        if (str_starts_with($contentUpper, 'NOTICE:')) {
            return self::STATUS_NOTICE;
        }

        if ($statusCode !== null && $statusCode >= 400) {
            return self::STATUS_ERROR;
        }

        // Raw data payloads (version, lists, reports) carry no prefix; VICIdial still
        // prints "ERROR" somewhere in the body when a report request is rejected.
        if (str_contains($contentUpper, 'ERROR')) {
            return self::STATUS_ERROR;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * Body starts with a tag: <!DOCTYPE html>, <html>, <h1>Bad Gateway</h1>, <?xml ...
     */
    private static function looksLikeHtml(string $content): bool
    {
        return preg_match('/^\s*<(!doctype|\?xml|[a-z][a-z0-9]*)[\s>\/]/i', $content) === 1;
    }

    /**
     * @param  array<string, string>  $headers
     */
    private static function headerValue(array $headers, string $name): ?string
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }
}
