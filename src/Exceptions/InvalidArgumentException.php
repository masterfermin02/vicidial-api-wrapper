<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Thrown when a method is called with arguments the wrapper can tell are wrong
 * before any request is sent (missing phone number, unknown update_fields key, ...).
 *
 * It is a programmer error, not a transport or VICIdial error, so callers can
 * catch it separately from TransportException / RetryException. It still extends
 * VicidialException, so a catch-all on the base class keeps working.
 */
final class InvalidArgumentException extends VicidialException {}
