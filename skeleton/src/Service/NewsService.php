<?php

namespace App\Service;

class NewsService
{
    public function __construct(private readonly HttpClientService $client)
    {
    }

    function getNewsTape(string $host, string $userRoute, /*string $newsRoute, string $contentRoute,*/ int $page): array {
        //взятие данных пользователей
        $user = $this->client->getInformation($host, $userRoute, $page);
        $authors = $this->client->getIdFromData($user, 'id', 'author',0); // взятие id авторов для нахождения о ним их заисей
        $content = $this->client->getInformationByIdSearch($host, $user, 'id', 'contents', 'author', $authors); // взятие записей пользователей по id авторов (пользователей)

        //приведение массива к одному виду
        $contentArray = $this->client->setArrayToOrdinaryType($content); // упорядочивание массива к одному типу вложенности элементов (для корректного перебора в последующих функциях)

        $contents = $this->client->getIdFromData($contentArray, 'id', 'content', 0); // взятие Id заисей для поиска по ним новостей

        $news = $this->client->getInformationByIdSearch($host, $content, 'id', 'news', 'content', $contents); // поиск новостей по Id заисям

        //приведение массива к одному виду
        $newsArray = $this->client->setArrayToOrdinaryType($news);// упорядочивание массива к одному типу вложенности элементов (здесь - для вывода)

        // вывод массивов
        dump($user);
        dump($contentArray);
        dump($newsArray);

        dd(0);

        return [];
    }
}