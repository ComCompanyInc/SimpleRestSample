<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\HttpClientService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiProvider implements ProviderInterface
{
    public function __construct(private readonly HttpClientService $client, private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $apiHost = $this->parameterBag->get('api_base_url');
        // TODO: Implement provide() method.

        return $this->client->getInformation($apiHost);
    }
}