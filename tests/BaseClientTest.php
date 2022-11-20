<?php

namespace Vicidial\Api\Tests;

use GuzzleHttp\Psr7\Response;
use Vicidial\Api\Tests\Fakers\FakeGuzzleClient;
use Vicidial\Api\Tests\Fakers\FakeGuzzleClientException;
use Vicidial\Api\Wrapper\Exceptions\InvalidUrlException;
use Vicidial\Api\Wrapper\BaseClient;

class BaseClientTest extends TestCase
{
    protected BaseClient $baseClient;

    public function setUp(): void
    {
        $this->baseClient = BaseClient::make(
            'testUser',
            'testPassword'
        );
    }

    /**
     * @param string $url
     * @param array  $options
     * @param string $expectedException
     *
     * @dataProvider dataProviderExceptions
     *
     * @throws \Exception
     */
    public function testInvalidParameterException(string $url, array $options, string $expectedException): void
    {
        $this->expectException($expectedException);

        $this->baseClient->callApiUrl($url, $options);
    }

    /**
     * @param        $url
     * @param        $options
     * @param string $expectedException
     *
     * @dataProvider dataProviderParamType
     *
     * @throws \Exception
     */
    public function testInvalidParameterType($url, $options, string $expectedException): void
    {
        $this->expectException($expectedException);

        $this->baseClient->callApiUrl($url, $options);
    }

    public function testCallApiUrlException(): void
    {
        $this->expectException(\Exception::class);

        $baseClient = BaseClient::make(
            'test',
            'test',
            'test',
            new FakeGuzzleClientException()
        );
        $baseClient->callApiUrl('http:://testtss.com', []);

    }

    public function testCallApiUrl(): void
    {
        $baseClient = BaseClient::make(
            'test',
            'test',
            'test',
            new FakeGuzzleClient()
        );
       $response = $baseClient->callApiUrl('http://testts.com', []);

       $this->assertEquals('', $response);
    }

    public function dataProviderExceptions(): array
    {
        return [
            'Empty url'                => ['', [], InvalidUrlException::class],
            'invalid url'        => ['http:/jajajja', [], InvalidUrlException::class],
        ];
    }

    public function dataProviderParamType(): array
    {
        return [
            [0, [], InvalidUrlException::class],
            ['htt://test.com', '', \TypeError::class],
            ['htt://test.com', -1, \TypeError::class],
            ['htt://test.com', 0, \TypeError::class],
            ['htt://test.com', 1, \TypeError::class],
        ];
    }
}
