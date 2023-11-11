<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport\Service\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

interface HttpClientInterface
{
    /**
     * @param array $headers
     * @param string $baseUri
     * 
     * @return GuzzleHttp\Client
     */
    public function create(array $headers, string $baseUri): Client;

    /**
     * @param string $url
     * @param string $body
     * 
     * @return Response
     */
    public function post(string $url, string $body): Response;

    /**
     * @param string $url
     * @param array #query
     * 
     * @return Response
     */
    public function get(string $url, array $query = []): Response;
}