<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class HttpClient implements HttpClientInterface
{
    /**
     * @var GuzzleHttp\Client
     */
    protected Client $client;

    /**
     * @param array $headers
     * @param string $baseUri
     * 
     * @return GuzzleHttp\Client
     */
    public function create(array $headers, string $baseUri = ''): Client
    {
        $this->client = new Client([
            'headers'  => $headers,
            'base_uri' => $baseUri,
            'timeout'  => 30.0
        ]);

        return $this->client;
    }

    /**
     * @param string $url
     * @param string $body
     * 
     * @return Response
     */
    public function post(string $url, string $body): Response 
    {
        return $this->client->post(
            $url,
            [
                'body' => $body
            ]
            
        );
    }

    /**
     * @param string $url
     * @param array #query
     * 
     * @return Response
     */
    public function get(string $url, array $query = []): Response
    {
        return $this->client->get(
            $url,
            ['query' => $query] 
        );
    }

}