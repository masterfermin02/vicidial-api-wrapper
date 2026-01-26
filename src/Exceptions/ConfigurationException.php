<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/**
 * Configuration error exception.
 *
 * Thrown when there are configuration errors such as invalid host,
 * missing required parameters, invalid timeout values, etc.
 *
 * @example
 * try {
 *     $config = VicidialConfig::create(
 *         host: '',
 *         user: 'user',
 *         pass: 'pass'
 *     );
 * } catch (ConfigurationException $e) {
 *     echo "Configuration error: " . $e->getMessage();
 * }
 */
final class ConfigurationException extends VicidialException {}
