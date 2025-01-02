<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\HttpClientService;
use http\Client;

class ApiProvider implements ProviderInterface
{
    private HttpClientService $client;
    public function __construct(HttpClientService $client)
    {
        $this->client = $client;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // TODO: Implement provide() method.

        return $this->client->fetchGitHubInformation();
    }
}