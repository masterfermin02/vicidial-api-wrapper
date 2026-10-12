<?php

declare(strict_types=1);

namespace VicidialApi\Exceptions;

/*
 * Backward-compatibility alias.
 *
 * The class used to live in this file under the name TransportException, which
 * PSR-4 autoloading could not resolve (file name and class name did not match),
 * so any network error raised a "Class not found" fatal instead of the intended
 * exception. The class now lives in TransportException.php; this file only keeps
 * the legacy name resolvable.
 *
 * @deprecated Use TransportException instead. Will be removed in the next major version.
 */
class_alias(TransportException::class, __NAMESPACE__.'\TransporterException');
