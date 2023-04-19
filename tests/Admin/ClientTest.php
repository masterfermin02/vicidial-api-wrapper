<?php

namespace Admin;

use PHPUnit\Framework\TestCase;
use Vicidial\Api\Tests\Fakers\FakeGuzzleClient;
use Vicidial\Api\Tests\Fakers\FakeGuzzleClientException;
use Vicidial\Api\Wrapper\Admin\Client;
use Vicidial\Api\Wrapper\BaseClient;
use Vicidial\Api\Wrapper\Exceptions\InvalidUrlException;

class ClientTest extends TestCase
{
    protected BaseClient $baseClient;

    public function setUp(): void
    {
        $this->baseClient = Client::create(
            'localhost',
            'testUser',
            'testPassword',
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

        $baseClient = \Vicidial\Api\Wrapper\Admin\Client::create(
            'localhost',
            'test',
            'test',
            'test',
            true,
            new FakeGuzzleClientException()
        );
        $baseClient->callApiUrl('https:://testtss.com', []);

    }

    public function testCallApiUrl(): void
    {
        $baseClient = Client::create(
            'localhost',
            'test',
            'test',
            'test',
            true,
            new FakeGuzzleClient()
        );
        $response = $baseClient->callApiUrl('https://testts.com', []);

        $this->assertEquals('', $response);
    }

    public function testMohList()
    {
        $baseClient = Client::create(
            'localhost',
            'test',
            'test',
            'test',
            true,
            new FakeGuzzleClient()
        );
        $response = $baseClient->mohList();

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
