<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\HttpClientService;
use App\Service\NewsService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiProvider implements ProviderInterface
{
    public function __construct(private readonly NewsService $newsService, private readonly ParameterBagInterface $parameterBag, private readonly RequestStack $requestStack)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();

        $userIdForGettingNews = $request->query->get('idUser');
        $apiHost = $this->parameterBag->get('api_base_url');
        // TODO: Implement provide() method.

        return $this->newsService->getNewsByAuthor($apiHost, '/api/users', $userIdForGettingNews, /*'/api/news', '/api/contents',*/ 1);
    }
}