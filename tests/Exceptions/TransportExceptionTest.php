<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use VicidialApi\Exceptions\TransporterException;
use VicidialApi\Exceptions\TransportException;
use VicidialApi\Exceptions\VicidialException;

/**
 * Regression: the class used to live in TransporterException.php, so PSR-4 autoloading
 * could not find TransportException and the network-error path died with
 * "Class not found" instead of throwing the intended exception.
 */
final class TransportExceptionTest extends TestCase
{
    public function test_transport_exception_is_autoloadable(): void
    {
        $this->assertTrue(class_exists(TransportException::class));
    }

    public function test_legacy_name_still_resolves_to_the_same_class(): void
    {
        $this->assertTrue(class_exists(TransporterException::class));

        $exception = new TransportException($this->networkException('connection refused'));

        $this->assertInstanceOf(TransporterException::class, $exception);
        $this->assertInstanceOf(VicidialException::class, $exception);
        $this->assertSame('connection refused', $exception->getMessage());
        $this->assertInstanceOf(NetworkExceptionInterface::class, $exception->getPrevious());
    }

    private function networkException(string $message): NetworkExceptionInterface
    {
        return new class($message) extends \RuntimeException implements NetworkExceptionInterface
        {
            public function getRequest(): RequestInterface
            {
                throw new \LogicException('not needed');
            }
        };
    }
}
