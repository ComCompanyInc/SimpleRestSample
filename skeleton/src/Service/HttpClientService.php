<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientService
{
    public function __construct(
        private readonly HttpClientInterface $client
    ) {
    }

    /**
     * Берет массив данных по роуту
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getInformation(string $host, string $route, int $page = 1, string $filter = null): array
    {

        if($filter == null) {
            $response = $this->client->request(
                'GET',
                $host . $route . '?page=' . $page
            );
        } else {
            $response = $this->client->request(
                'GET',
                $host . $route . '?page=' . $page . '&' . $filter
            );
        }

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }

    /**
     * Формирует массив данных из другой таблицы по ключу из заиси (ключ берется из массива ids, если он пустой то берется из самого массива с данными - $data)
     * @param string $host
     * Хост сайта
     * @param array $data
     * Сформированный массив с данными
     * @param string $searchingField
     * Ключ, по которому производить поиск
     * @param string $searchingFieldPlural
     * Название таблицы во множественном числе (о названию роута в API, где хранятся данные)
     * @param string $filter
     * Название параметра для фильтра в другой таблице
     * @param array $ids
     * Массив с ключами по которому производить поиск в API по заданному фильтру
     * @return array
     * Возвращает массив с данными отфильтрованными по фильтру API
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getInformationByIdSearch(string $host, array $data, string $searchingField, string $searchingFieldPlural, string $filter, array $ids = []): array
    {
        $result = [];

        if(empty($ids)) {
            foreach ($data as $item) {
                foreach ($item as $key => $value) {
                    if ($key == $searchingField) {
                        $result[] = $this->getInformation($host, '/api/' . $searchingFieldPlural, 1, $filter . '=' . $value);
                    }
                }
            }
        } else {
            foreach ($ids as $value) {
                $result[] = $this->getInformation($host, '/api/' . $searchingFieldPlural, 1, $filter . '=' . $value);
            }
        }

        return $result;
    }

    /**
     * Взять все Id из массива данных по названию поля (для фильтров таблиц API)
     * @param array $data
     * Сформированный массив с данными
     * @param string $searchingField
     * Ключ, по которому производить поиск
     * @param string $nameFieldForFilter
     * Название параметра для фильтра в другой таблице
     * @param int $amountOfCutSymbols
     * Количество символов которое надо вырезать из строки с ключом чтобы достать только ключ (без роута)
     * @return array
     * Возвращает сформированный массив с ключами для фильтров таблиц API (поиску по ним данных)
     */
    public function getIdFromData(array $data, string $searchingField, string $nameFieldForFilter, int $amountOfCutSymbols/*, int $lengthOfElements*/): array {
        $result = [];

        foreach($data as $item) {
            foreach ($item as $key => $value) {
                //dump('value=');
                //dump($value);
                if ($key == $searchingField) {
                    if(!empty($value)) {
                        if(is_array($value)) {
                            $result[] = '&' . $nameFieldForFilter . '[]=' . substr($value[0], $amountOfCutSymbols);
                        } else {
                            $result[] = '&' . $nameFieldForFilter . '[]=' . substr($value, $amountOfCutSymbols);
                        }
                    }
                }
            }
        }

//        dump('____________________');

        return $this->divideArrayForFilter($result, 15);

        //return $result;
    }

    /**
     * Приводит массив к одному типу вложенности элементов (для корректного еребора в последующих функциях)
     * @param array $data
     * @return array
     */
    public function setArrayToOrdinaryType(array $data): array {
        $result = [];

        foreach ($data as $item) {
            if(!empty($item)) {
                foreach ($item as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Разбить строку ключей для фильтра API на несколько частей
     * @param array $data
     * Массив с ключами для фильтра API таблицы
     * @param int $itemsForDivision
     * количество ключей в одной строке
     * @return array
     */
    public function divideArrayForFilter(array $data, int $itemsForDivision = 30): array {
        $result = [];

        // Разбиваем массив на части по $itemsForDivision элементов
        $chunks = array_chunk($data, $itemsForDivision);

        // Объединяем элементы каждого чанка в строку
        foreach ($chunks as $chunk) {
            $result[] = implode('', $chunk);
        }

        return $result;
    }

    /**
     * Сформировать массив данных из исходного массива по фильтру определенного поля с определенным значением
     * @param array $data
     * Массив данных который нужно отфильтровать по значению определенного поля
     * @param string $fieldFilterName
     * имя поля по которому будет производиться фильтрация
     * @param string $fieldFilterValue
     * значение поля по которому будет производиться фильтрация
     * @return array
     * Отфильтрованный массив по значению определенного поля
     */
    public function filterDataByField(array $data, string $fieldFilterName, string $fieldFilterValue): array {
        $result = [];

        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                if(($key == $fieldFilterName) && ($value == $fieldFilterValue)) {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }
}