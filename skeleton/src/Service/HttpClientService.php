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

    public function getInformationByIdSearch(string $host, array $data, string $searchingField, string $searchingFieldPlural, string $filter = null): array
    {
        $result = [];

        //dump('________foreach_________');
        foreach($data as $item) {
            foreach ($item as $key => $value) {
                //dump($key);
                if ($key == $searchingField) {
//                    dump('/api/' . $searchingFieldPlural);
//                    dd($filter . '=' . $value);
                    $result[] = $this->getInformation($host, '/api/' . $searchingFieldPlural, 1, $filter . '=' . $value);
                }
            }
        }
        //dump('________end foreach_________');

        return $result;
    }

//    public function dataReview(string $host, string $route, int $amountOfPageElements): array {
//
//        while(true) {
//           if()
//
//            $this->getInformation($host, $route, );
//        }
//    }

}