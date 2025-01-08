<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NewsService
{
    public function __construct(private readonly HttpClientService $client)
    {
    }

    /**
     * Получить новость автора по его Id
     * @param string $host
     * Хост сайта
     * @param string $userRoute
     * путь API к получению пользователей
     * @param string $userId
     * Id пользователя у которого нужно взять новости
     * @param int $page
     * Страница (на одной странице 30 новостей) - для агинации
     * @return array
     * Сформированный массив с новостями определенного (выбранного) автора
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    function getNewsByAuthor(string $host, string $userRoute, string $userId, /*string $newsRoute, string $contentRoute,*/ int $page): array {
        //взятие данных пользователей
        $user = $this->client->filterDataByField($this->client->getInformation($host, $userRoute, $page), 'id', $userId);
        $authors = $this->client->getIdFromData($user, 'id', 'author',0); // взятие id авторов для нахождения о ним их заисей
        $content = $this->client->getInformationByIdSearch($host, $user, 'id', 'contents', 'author', $authors); // взятие записей пользователей по id авторов (пользователей)

        //приведение массива к одному виду
        $contentArray = $this->client->setArrayToOrdinaryType($content); // упорядочивание массива к одному типу вложенности элементов (для корректного перебора в последующих функциях)

        $contents = $this->client->getIdFromData($contentArray, 'id', 'content', 0); // взятие Id записей для поиска по ним новостей

        $news = $this->client->getInformationByIdSearch($host, $content, 'id', 'news', 'content', $contents); // поиск новостей по Id заисям

        $newsArray = $this->client->setArrayToOrdinaryType($news); // упорядочивание массива к одному типу вложенности элементов (для корректного перебора в последующих функциях)

        $newsId = $this->client->getIdFromData($newsArray, 'id', 'news', 0); // взятие Id записей для поиска по ним комментариев

        $contentNews = $this->client->getInformationByIdSearch($host, $news, 'id', 'content_news', 'news', $newsId); // получение связующей таблицы с комментариями к новости

        $contentNewsArray = $this->client->setArrayToOrdinaryType($contentNews); // упорядочивание массива к одному типу вложенности элементов (для корректного перебора в последующих функциях)

        $comments = $this->client->getIdFromData($contentNewsArray, 'content', 'id', 14); // получение Id комментариев к новости по связующей таблице

        $comment = $this->client->getInformationByIdSearch($host, $content, 'id', 'contents', 'id', $comments); // получение самих комментариев к новости по Id
        $commentArray = $this->client->setArrayToOrdinaryType($comment); // упорядочивание массива к одному типу вложенности элементов (для корректного перебора в последующих функциях)

        // вывод массивов
//        dump($user);
//        dump($contentArray);
//        dump($newsArray);

        // формирование массива новостей с их авторами для ответа
        $result = [];

        foreach ($user as $userItem) { // перебор массива с пользователями
            $news = []; // массив для сбора множества новостей одной персоны
            foreach ($contentArray as $contentItem) { // перебор массива с содержимым новости и комментария
                foreach ($newsArray as $newsItem) { // перебор массива новостей
                    if(substr($contentItem['author'], 11) != $userItem['id']) {
                        continue;
                    }// если ключи из таблиц User и Content совпадают то продолжать

                    if (substr($newsItem['content'], 14) != $contentItem['id']) {
                        continue;
                    }// если ключи из таблиц News и Content совпадают то продолжать

                    $news[] = ['title' => $newsItem, 'text' => $contentItem]; // добавить в массив news новый элемент с заголовком и содержимым новости

                }
            }

            // формирование ответа (в цикле персон т.к. новости нужно взять у всех персон)
            $result[] = [
                'author' => $userItem,
                'news' => $news,
                'comments' => $commentArray
            ];
        }

        return $result;
    }

    /**
     * Получить выбранную новость
     * @return void
     */
    function getNew() {
        //TODO: Сделать вывод информации о выбранной новости (о ее id, или как то так) из новостной ленты
    }

    /**
     * Получить новости с новостной ленты
     * @return void
     */
    function getNewsTape() {
        //TODO: Сделать новостную ленту которая возвращает краткую информацию о новости и автора
    }
}