<?php

declare(strict_types=1);

namespace VicidialApi\Diagnostics;

use Closure;
use Throwable;
use VicidialApi\Client;
use VicidialApi\Exceptions\RetryException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Responses\VicidialResponse;
use VicidialApi\VicidialClient;

/**
 * Connectivity and permission check against a VICIdial server.
 *
 * Four probes, cheapest first:
 *
 * 1. Agent API `version`: no credentials needed, proves the URL and proxy path to /agc/api.php
 * 2. Agent API `webserver`: needs the API user, proves credentials and Agent API permission
 * 3. Non-Agent API `version`: no credentials needed, proves the path to /vicidial/non_agent_api.php
 * 4. Non-Agent API `campaigns_list`: needs user_level 8+ and "view reports", proves Non-Agent permissions
 *
 * Failures are explained in plain language (wrong URL, wrong password, missing user level, ...).
 * Framework-free; the Laravel `vicidial:ping` command is a thin wrapper around it.
 */
final class Ping
{
    public function __construct(private readonly VicidialClient|Client $client) {}

    public function run(): PingReport
    {
        $agent = $this->client->agent();
        $admin = $this->client->admin();

        return new PingReport([
            $this->probe('Agent API reachable', 'version', fn (): VicidialResponse => $agent->versionResponse()),
            $this->probe('Agent API credentials', 'webserver', fn (): VicidialResponse => $agent->webserverResponse()),
            $this->probe('Non-Agent API reachable', 'version', fn (): VicidialResponse => $admin->versionResponse()),
            $this->probe('Non-Agent API credentials', 'campaigns_list', fn (): VicidialResponse => $admin->campaignsListResponse([])),
        ]);
    }

    /**
     * Plain-language explanation of a failed response.
     */
    public static function explain(VicidialResponse $response): string
    {
        $message = $response->message() ?? $response->raw();
        $upper = strtoupper($message);
        $statusCode = $response->statusCode();

        if ($statusCode === 401 || $statusCode === 403) {
            return "The web server rejected the request with HTTP $statusCode before VICIdial saw it. If the server uses HTTP basic auth, configure basicAuthUser / basicAuthPass; also check IP allow-lists on the web server or firewall.";
        }

        if ($statusCode === 404) {
            return 'HTTP 404: the API script was not found at this path. Check the host and protocol; the Agent API lives at /agc/api.php and the Non-Agent API at /vicidial/non_agent_api.php.';
        }

        if ($statusCode !== null && $statusCode >= 500) {
            return "HTTP $statusCode from the web server or a proxy in front of VICIdial, not an API answer. Check Apache/PHP on the VICIdial web server and any load balancer.";
        }

        if (str_contains($upper, 'EMPTY RESPONSE BODY')) {
            return 'The server answered with an empty body. Usually the host is right but the path is not, or a proxy stripped the response. Check protocol (http vs https) and the base URL.';
        }

        if (str_contains($upper, 'HTML RESPONSE')) {
            return 'The server answered with an HTML page instead of API text: typically a login page, a 403/404 page or a proxy error. Check the base URL and that the request reaches the VICIdial web server directly.';
        }

        if (str_contains($upper, 'INVALID USERNAME') || str_contains($upper, 'LOGIN INCORRECT') || str_contains($upper, 'INVALID USER/PASS')) {
            return 'VICIdial rejected the API user name or password. Verify the user exists in Admin > Users and the password matches (note that VICIdial user passwords are case-sensitive).';
        }

        if (str_contains($upper, 'DOES NOT HAVE API ACCESS')) {
            return 'The API user exists but "API Access" is not enabled for it. In Admin > Users open the user and set "Agent API Access" / "API Access" to Y (modify_leads = 1 is needed for lead writes).';
        }

        if (str_contains($upper, 'DOES NOT HAVE PERMISSION') || str_contains($upper, 'NOT ALLOWED FOR USER')) {
            return 'The API user is authenticated but lacks permission for this function. Raise its user_level (7 for most reads, 8 for lists and campaign data, 9 for user and phone administration), enable "View Reports" for report functions, and check the user group has access to the campaign.';
        }

        if (str_contains($upper, 'NO FUNCTION SPECIFIED') || str_contains($upper, 'MISSING VARIABLES')) {
            return 'VICIdial received the request but not the expected query parameters. A proxy or rewrite rule is probably dropping the query string.';
        }

        if (str_contains($upper, 'NO VIEWABLE CAMPAIGNS')) {
            return 'Credentials and permissions are fine; this user simply has no campaigns it may view, so campaigns_list has nothing to return.';
        }

        return 'VICIdial answered: '.$message;
    }

    /**
     * Plain-language explanation of a transport failure.
     */
    public static function explainException(Throwable $exception): string
    {
        $message = $exception->getMessage();
        $lower = strtolower($message);

        if ($exception instanceof RetryException) {
            $inner = $exception->getPrevious()?->getPrevious() ?? $exception->getPrevious() ?? $exception;

            return sprintf('Gave up after %d attempts. Last error: %s', $exception->getAttemptsMade(), self::explainException($inner));
        }

        if ($exception instanceof TransportException && $exception->getPrevious() instanceof Throwable) {
            return self::explainException($exception->getPrevious());
        }

        if (str_contains($lower, 'could not resolve host') || str_contains($lower, 'getaddrinfo') || str_contains($lower, 'name or service not known')) {
            return "DNS lookup failed ($message). Check the host name in the configuration.";
        }

        if (str_contains($lower, 'connection refused')) {
            return "Connection refused ($message). The host is reachable but nothing listens on that port: check the protocol (http vs https) and firewall rules.";
        }

        if (str_contains($lower, 'timed out') || str_contains($lower, 'timeout')) {
            return "Timed out ($message). The host does not answer: check network routes, firewall and the timeout / connectTimeout settings.";
        }

        if (str_contains($lower, 'ssl') || str_contains($lower, 'certificate')) {
            return "TLS problem ($message). The certificate is self-signed or does not match the host; fix the certificate or set verifyTls: false for testing only.";
        }

        return $message;
    }

    /**
     * @param  Closure(): VicidialResponse  $call
     */
    private function probe(string $name, string $function, Closure $call): EndpointCheck
    {
        try {
            $response = $call();
        } catch (Throwable $exception) {
            return EndpointCheck::failed($name, $function, $exception::class.': '.$exception->getMessage(), self::explainException($exception));
        }

        if ($response->ok()) {
            return EndpointCheck::passed($name, $function, $this->summarize($function, $response), $response);
        }

        return EndpointCheck::failed($name, $function, $response->message() ?? $response->raw(), self::explain($response), $response);
    }

    private function summarize(string $function, VicidialResponse $response): string
    {
        if ($function === 'version') {
            $version = $response->get('version');
            $build = $response->get('build');

            if (is_string($version)) {
                return 'VICIdial '.$version.(is_string($build) ? ' build '.$build : '');
            }
        }

        if ($function === 'webserver') {
            $server = $response->get('server name') ?? $response->get('host name');

            return is_string($server) && $server !== '' ? 'authenticated, web server '.$server : 'authenticated';
        }

        if ($function === 'campaigns_list') {
            $rows = count($response->rows());

            return $rows > 0 ? "authenticated, $rows campaign(s) visible" : 'authenticated';
        }

        return 'ok';
    }
}
