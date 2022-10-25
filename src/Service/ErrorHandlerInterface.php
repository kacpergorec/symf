<?php
declare (strict_types=1);

namespace App\Service;

use Exception;

/**
 *
 * An interface for adding Errors to a class for later display.
 *
 * @package App\Service
 */
interface ErrorHandlerInterface
{
    public function addError(Exception $error): void;

    public function getErrors(): array;
}