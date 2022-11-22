<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractCompanyService extends AbstractService
{
    protected HttpClientInterface $client;
    protected CacheInterface $cache;

    abstract public function fetchHistoricalData(string $companySymbol): array;
    abstract public function fetchCompanySymbolInformation(): array;
    abstract public function fetchCompanyName(string $companySymbol): string;
    abstract public function fetchChartInformation(array $historicalData): array;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param HttpClientInterface $client
     * @param CacheInterface $cache
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        HttpClientInterface $client,
        CacheInterface $cache
    ) {
        parent::__construct($parameterBag);

        $this->client = $client;
        $this->cache = $cache;
    }
}
