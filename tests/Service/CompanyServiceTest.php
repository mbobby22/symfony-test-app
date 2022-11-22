<?php

namespace App\Tests\Service;

use App\Service\CompanyService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;

class CompanyServiceTest extends KernelTestCase
{
    public function testFetchHistoricalData()
    {
        $companySymbol = 'TEST';
        $priceResult = [
            [
                "date" => 1668522600,
                "open" => 0.20499999821186066,
                "high" => 0.20499999821186066,
                "low" => 0.20499999821186066,
                "close" => 0.20499999821186066,
                "volume" => 0,
                "adjclose" => 0.20499999821186066
            ],
        ];
        $expectedResponseData = [
            'prices' => [
                [
                    "date" => 1668522600,
                    "open" => 0.20499999821186066,
                    "high" => 0.20499999821186066,
                    "low" => 0.20499999821186066,
                    "close" => 0.20499999821186066,
                    "volume" => 0,
                    "adjclose" => 0.20499999821186066
                ],
            ],
            "isPending" => false,
            "firstTradeDate" => 733674600,
            "id" => "",
            "timeZone" => [
                "gmtOffset" => -18000,
            ],
            "eventsData" => [],
        ];

        self::bootKernel();
        $container = static::getContainer();

        $cacheMock = $this->createMock(CacheInterface::class);
        $cacheMock
            ->method('get')
            ->with(md5($companySymbol))
            ->willReturn($expectedResponseData)
        ;

        $mockResponseJson = json_encode([], JSON_THROW_ON_ERROR);
        $mockResponse = new MockResponse($mockResponseJson, []);
        $httpClientMock = new MockHttpClient($mockResponse, CompanyService::HISTORICAL_DATA_REQUEST_URL);

        /** @var CompanyService $companyServiceMock */
        $companyService = new CompanyService($container->getParameterBag(), $httpClientMock, $cacheMock);

        $result = $companyService->fetchHistoricalData($companySymbol);
        $this->assertEquals($priceResult, $result);
    }

    public function testMakeHistoricalDataRequest()
    {
        $companySymbol = 'TEST';
        $priceResult = [
            [
                "date" => 1668522600,
                "open" => 0.20499999821186066,
                "high" => 0.20499999821186066,
                "low" => 0.20499999821186066,
                "close" => 0.20499999821186066,
                "volume" => 0,
                "adjclose" => 0.20499999821186066
            ],
        ];

        self::bootKernel();

        $container = static::getContainer();

        $cacheMock = $this->createMock(CacheInterface::class);

        $expectedResponseData = [
            'prices' => [
                [
                    "date" => 1668522600,
                    "open" => 0.20499999821186066,
                    "high" => 0.20499999821186066,
                    "low" => 0.20499999821186066,
                    "close" => 0.20499999821186066,
                    "volume" => 0,
                    "adjclose" => 0.20499999821186066
                ],
            ],
            "isPending" => false,
            "firstTradeDate" => 733674600,
            "id" => "",
            "timeZone" => [
                "gmtOffset" => -18000,
            ],
            "eventsData" => [],
        ];
        $mockResponseJson = json_encode($expectedResponseData, JSON_THROW_ON_ERROR);
        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 200,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClientMock = new MockHttpClient($mockResponse, CompanyService::HISTORICAL_DATA_REQUEST_URL);

        /** @var CompanyService $companyServiceMock */
        $companyService = new CompanyService($container->getParameterBag(), $httpClientMock, $cacheMock);

        $response = $companyService->makeHistoricalDataRequest($companySymbol);
        $this->assertEquals($priceResult, $response->toArray()['prices']);
    }

//    public function fetchCompanySymbolInformation()
//    {
//        self::bootKernel();
//
//        $container = static::getContainer();
//
//        /** @var CompanyService $companyServiceMock */
//        $companyServiceMock = $container->get(CompanyService::class);
//        $result = $companyServiceMock->fetchCompanySymbolInformation();
//
//        $this->assertEquals('...', $result);
//    }
//
//    public function testFetchCompanyName()
//    {
//        $companySymbol = 'TEST';
//
//        self::bootKernel();
//
//        $container = static::getContainer();
//
//        /** @var CompanyService $companyServiceMock */
//        $companyServiceMock = $container->get(CompanyService::class);
//        $result = $companyServiceMock->fetchCompanyName($companySymbol);
//
//        $this->assertEquals('...', $result);
//    }
//
//    public function testFetchChartInformation()
//    {
//        $companySymbol = 'TEST';
//        $dateFormat = 'Y-m-d';
//
//        self::bootKernel();
//
//        $container = static::getContainer();
//
//        /** @var CompanyService $companyServiceMock */
//        $companyServiceMock = $container->get(CompanyService::class);
//        $result = $companyServiceMock->fetchChartInformation($companySymbol, $dateFormat);
//
//        $this->assertEquals('...', $result);
//    }
}
