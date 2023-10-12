<?php

declare(strict_types=1);

namespace Payone\Sdk\Container;

use Payone\Sdk\SdkExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author Fabian Böttcher <me@cakasim.de>
 * @since 0.1.0
 */
class NotFountException extends ContainerException implements NotFoundExceptionInterface, SdkExceptionInterface
{
}
