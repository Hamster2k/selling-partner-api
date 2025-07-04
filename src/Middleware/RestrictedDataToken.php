<?php

declare(strict_types=1);

namespace SellingPartnerApi\Middleware;

use Saloon\Contracts\RequestMiddleware;
use Saloon\Http\PendingRequest;
use SellingPartnerApi\Enums\Endpoint;

class RestrictedDataToken implements RequestMiddleware
{
    public function __construct(
        protected string $path,
        protected string $method,
        protected array $knownDataElements,
    ) {}

    public function __invoke(PendingRequest $pendingRequest): void
    {
        $connector = $pendingRequest->getConnector();
        if (
            ! Endpoint::isSandbox($connector->endpoint) || 
            str_contains($pendingRequest->getUri()->getPath(), 'shippingLabels') 
        ) {
            $pendingRequest->authenticate($connector->restrictedAuth(
                $this->path,
                $this->method,
                $this->knownDataElements
            ));
        }
    }
}
