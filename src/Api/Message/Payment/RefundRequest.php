<?php

declare(strict_types=1);

namespace Payone\Sdk\Api\Message\Payment;

use Payone\Sdk\Api\Message\Payment\Parameter\Amount;
use Payone\Sdk\Api\Message\Payment\Parameter\Currency;
use Payone\Sdk\Api\Message\Payment\Parameter\SequenceNumber;
use Payone\Sdk\Api\Message\Payment\Parameter\TransactionId;
use Payone\Sdk\Api\Message\Request;

/**
 * Represents a payment refund request.
 *
 * @author Fabian Böttcher <me@cakasim.de>
 * @since 0.1.0
 */
class RefundRequest extends Request implements RefundRequestInterface
{
    use TransactionId,
        SequenceNumber,
        Amount,
        Currency;

    /**
     * @inheritDoc
     */
    public function __construct(array $parameters)
    {
        parent::__construct(array_merge($parameters, [
            'request' => 'refund',
        ]));
    }
}
