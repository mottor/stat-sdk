<?php

namespace Mottor\Stat\Domain\Visit\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Mottor\Api\Domain\Request\Model\PostRequest;
use Mottor\Api\Domain\Response\Model\JsonResponse;
use Mottor\Stat\Domain\Visit\ValueObject\Visit;
use Psr\Http\Message\RequestInterface;

class VisitService
{
    const API_METHOD_VISIT_ADD = 'visit.add';
    const API_METHOD_VISIT_BATCH_ADD = 'visit.batchAdd';

    /**
     * @var string
     */
    protected $baseApiUri;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @param string          $baseApiUri
     * @param string          $secretKey
     * @param ClientInterface $httpClient [optional]
     */
    public function __construct($baseApiUri, $secretKey, ClientInterface $httpClient = null) {
        $this->baseApiUri = $baseApiUri;
        $this->secretKey = $secretKey;

        if (null === $httpClient) {
            $httpClient = new Client();
        }

        $this->httpClient = $httpClient;
    }

    /**
     * Sends a data about single visit to remote service
     *
     * @inheritdoc
     */
    public function addVisit(Visit $visit) {
        $uri = $this->createUri(self::API_METHOD_VISIT_ADD);

        $postParameters = $visit->toArray();

        $request = new PostRequest($uri);
        $request = $request->withSecretKey($this->secretKey)
                           ->withParameters($postParameters);

        $response = $this->send($request);

        return $response->isSuccessful();
    }

    /**
     * Sends a data about group of visits to remote service
     *
     * @inheritdoc
     */
    public function batchAddVisit(array $visits) {
        $uri = $this->createUri(self::API_METHOD_VISIT_BATCH_ADD);

        $postParameters = [];

        foreach ($visits as $index => $visit) {
            $postParameters['data'][$index] = $visit->toArray();
        }

        $request = new PostRequest($uri);
        $request = $request->withSecretKey($this->secretKey)
                           ->withParameters($postParameters);

        $response = $this->send($request);

        return $response->isSuccessful();
    }

    /**
     * @param string $methodName
     *
     * @return string
     */
    protected function createUri($methodName) {
        return $this->baseApiUri . $methodName;
    }

    /**
     * Sends a PSR compatible request to an API,
     * after that returns a decorated response
     *
     * @param RequestInterface $request
     *
     * @return JsonResponse
     * @throws GuzzleException
     */
    protected function send(RequestInterface $request) {
        $response = $this->httpClient->send($request);

        $response = new JsonResponse(
            $response->getBody(),
            $response->getStatusCode(),
            $response->getHeaders()
        );

        return $response;
    }
}