<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\HttpClientService;
use http\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiProvider implements ProviderInterface
{
    private HttpClientService $client;
    public function __construct(HttpClientService $client, private ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $apiHost = $this->parameterBag->get('api_base_url');
        // TODO: Implement provide() method.

        return $this->client->getInformation($apiHost);
    }
}