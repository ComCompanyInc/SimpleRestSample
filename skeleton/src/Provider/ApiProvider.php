<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\HttpClientService;
use App\Service\NewsService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiProvider implements ProviderInterface
{
    public function __construct(private readonly NewsService $newsService, private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $apiHost = $this->parameterBag->get('api_base_url');
        // TODO: Implement provide() method.

        return $this->newsService->getNewsTape($apiHost, '/api/users', '/api/news', '/api/contents', 1);
    }
}