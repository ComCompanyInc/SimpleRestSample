<?php

namespace App\Service;

class NewsService
{
    public function __construct(private readonly HttpClientService $client)
    {
    }

    function getNewsTape(string $host, string $userRoute, string $newsRoute, string $contentRoute, int $page): array {

        $user = $this->client->getInformation($host, $userRoute, $page); //$this->client->getInformationByIdSearch($host, $content, 'author', 'users');
        $content = $this->client->getInformationByIdSearch($host, $user, 'id', 'contents', 'author'); //$this->client->getInformation($host, $contentRoute, $page);
        //$news = $this->client->getInformationByIdSearch($host, $this->client->getInformation($host, $newsRoute, $page), 'content', 'contents');//$this->client->getInformation($host, $newsRoute, $page);

        dump($user);
        dump($content);
        //dump($news);


        dd(0);

        return [];
    }
}