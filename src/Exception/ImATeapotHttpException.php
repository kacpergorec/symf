<?php
declare (strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ImATeapotHttpException extends HttpException
{
    public function __construct(?string $message = 'The server is unable to brew coffee due to the fact that it is permanently configured as a teapot.', Throwable $previous = null, int $code = 0, array $headers = [])
    {
        if (null === $message) {
            trigger_deprecation('symfony/http-kernel', '5.3', 'Passing null as $message to "%s()" is deprecated, pass an empty string instead.', __METHOD__);

            $message = '';
        }

        parent::__construct(418, $message, $previous, $headers, $code);
    }
}