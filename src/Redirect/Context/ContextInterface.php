<?php

declare(strict_types=1);

namespace Payone\Sdk\Redirect\Context;

use Payone\Sdk\Redirect\Token\TokenInterface;

/**
 * The context of an incoming redirect.
 *
 * @author Fabian Böttcher <me@cakasim.de>
 * @since 1.0.0
 */
interface ContextInterface
{
    /**
     * Returns the redirect token.
     *
     * @return TokenInterface The redirect token.
     */
    public function getToken(): TokenInterface;
}
