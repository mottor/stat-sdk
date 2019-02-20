<?php

namespace Mottor\Stat\Domain\Visit\Service;

use DateTimeInterface;
use Exception;
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

    const API_METHOD_VISIT_GET_BY_SITE_ID = 'visit.getBySiteId';

    const API_METHOD_VISIT_GET_BY_SITE_IDS = 'visit.getBySiteIds';

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
     * @param Visit $visit
     *
     * @return void
     * @throws Exception
     * @throws GuzzleException
     */
    public function addVisit(Visit $visit) {
        $uri = $this->createUri(self::API_METHOD_VISIT_ADD);

        $postParameters = $visit->toArray();

        $request = new PostRequest($uri);
        $request = $request->withSecretKey($this->secretKey)
                           ->withParameters($postParameters);

        $response = $this->send($request);

        $this->checkResponseStatus($response);
        $this->checkResponseMemberStatus($response);
    }

    /**
     * Sends a data about group of visits to remote service
     *
     * @param Visit[] $visits
     *
     * @return void
     * @throws Exception
     * @throws GuzzleException
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

        $this->checkResponseStatus($response);
        $this->checkResponseMemberStatus($response);
    }

    /**
     * @param int               $siteId
     * @param DateTimeInterface $dateStart
     * @param DateTimeInterface $dateEnd
     *
     * @return array
     * @throws Exception
     * @throws GuzzleException
     */
    public function getVisitBySiteId($siteId, DateTimeInterface $dateStart, DateTimeInterface $dateEnd) {
        $uri = $this->createUri(self::API_METHOD_VISIT_GET_BY_SITE_ID);

        $request = new PostRequest($uri);

        $dateStart = $dateStart->format(Visit::DATE_FORMAT);
        $dateEnd = $dateEnd->format(Visit::DATE_FORMAT);

        $request = $request
            ->withSecretKey($this->secretKey)
            ->withParameters([
                'siteId'    => $siteId,
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd
            ]);

        $response = $this->send($request);

        $this->checkResponseStatus($response);
        $this->checkResponseMemberStatus($response);

        return $response->getMember(JsonResponse::MEMBER_NAME_DATA);
    }

    /**
     * @param int[]             $siteIds
     * @param DateTimeInterface $dateStart
     * @param DateTimeInterface $dateEnd
     *
     * @return array
     * @throws Exception
     * @throws GuzzleException
     */
    public function getVisitBySiteIds(array $siteIds, DateTimeInterface $dateStart, DateTimeInterface $dateEnd) {
        $uri = $this->createUri(self::API_METHOD_VISIT_GET_BY_SITE_IDS);

        $request = new PostRequest($uri);

        $dateStart = $dateStart->format(Visit::DATE_FORMAT);
        $dateEnd = $dateEnd->format(Visit::DATE_FORMAT);

        $request = $request
            ->withSecretKey($this->secretKey)
            ->withParameters([
                'siteIds'   => $siteIds,
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd
            ]);

        $response = $this->send($request);

        $this->checkResponseStatus($response);
        $this->checkResponseMemberStatus($response);

        return $response->getMember(JsonResponse::MEMBER_NAME_DATA);
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

    protected function checkResponseStatus(JsonResponse $jsonResponse) {
        if (200 === $jsonResponse->getStatusCode()) {
            return;
        }

        if (null !== $jsonResponse->getReasonPhrase()) {
            $message = sprintf(
                'Bad response (code: %s, phrase: %s)',
                $jsonResponse->getStatusCode(),
                $jsonResponse->getReasonPhrase()
            );
        } else {
            $message = sprintf(
                'Bad response (code: %s)',
                $jsonResponse->getStatusCode()
            );
        }

        throw new Exception($message);
    }

    protected function checkResponseMemberStatus(JsonResponse $jsonResponse) {
        $status = $jsonResponse->getMember(JsonResponse::MEMBER_NAME_STATUS);

        if (true === $status) {
            return;
        }

        $error = $jsonResponse->getMember(JsonResponse::MEMBER_NAME_ERROR, []);

        if (isset($error['message'])) {
            $message = sprintf('Something has been wrong (%s)', $error['message']);
        } else {
            $message = 'Something has been wrong';
        }

        throw new Exception($message);
    }
}