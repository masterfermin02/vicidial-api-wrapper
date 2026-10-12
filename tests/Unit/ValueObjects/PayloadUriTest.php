<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\Transporter\BaseUri;
use VicidialApi\ValueObjects\Transporter\Headers;
use VicidialApi\ValueObjects\Transporter\Payload;
use VicidialApi\ValueObjects\Transporter\QueryParams;

final class PayloadUriTest extends TestCase
{
    public function test_base_uri_and_resource_are_joined_with_a_single_slash(): void
    {
        $payload = Payload::retrieveWithParameters('/agc/api.php', ['function' => 'version']);

        $request = $payload->toRequest(BaseUri::from('http://vicidial.test'), Headers::create(), QueryParams::create());

        $this->assertSame('http://vicidial.test/agc/api.php?function=version', (string) $request->getUri());
    }

    public function test_resource_without_leading_slash_still_works(): void
    {
        $payload = Payload::retrieveWithParameters('vicidial/non_agent_api.php', []);

        $request = $payload->toRequest(BaseUri::from('https://vicidial.test'), Headers::create(), QueryParams::create());

        $this->assertSame('https://vicidial.test/vicidial/non_agent_api.php', (string) $request->getUri());
    }
}
