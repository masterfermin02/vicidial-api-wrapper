<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Invalid argument exception.
 *
 * Thrown when an invalid argument is passed to a method.
 *
 * @deprecated Use ConfigurationException instead. Will be removed in v3.0.
 */
final class InvalidArgumentException extends VicidialException {}
