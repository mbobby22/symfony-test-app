<?php

namespace App\Service;

use App\Entity\CompanyHistory;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CompanyService extends AbstractCompanyService
{
    const COMPANY_INFO_REQUEST_URL =
        'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';
    const HISTORICAL_DATA_REQUEST_URL = 'https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data';
    const HISTORICAL_DATA_REQUEST_HOST = 'yh-finance.p.rapidapi.com';
    const HISTORICAL_DATA_REGION = 'US';
    const CACHE_TTL = 600;
    const DECIMAL_PRECISION = 3;

    public function makeHistoricalDataRequest(string $companySymbol): ResponseInterface
    {
        return $this->client->request('GET', self::HISTORICAL_DATA_REQUEST_URL, [
            'headers' => [
                'X-RapidAPI-Key' => $this->params->get('app.api.key'),
                'X-RapidAPI-Host' => self::HISTORICAL_DATA_REQUEST_HOST,
            ],
            'query' => [
                'symbol' => $companySymbol,
                'region' => self::HISTORICAL_DATA_REGION,
            ],
        ]);
    }

    public final function fetchHistoricalData(string $companySymbol): array
    {
        $cacheKey = md5($companySymbol);
        $cacheContent = $this->cache->get($cacheKey, function (ItemInterface $item) use ($companySymbol) {
            $item->expiresAfter(self::CACHE_TTL);

            return $this->makeHistoricalDataRequest($companySymbol)->toArray();
        });

        if (isset($cacheContent['prices'])) {
            return $cacheContent['prices'];
        }

        return $cacheContent;
    }

    public final function fetchCompanySymbolInformation(): array
    {
        $companyInformation = $this->fetchCompanyInformation();
        $companySymbol = [];

        if (empty($companyInformation)) {
            return [];
        }

        foreach ($companyInformation as $company) {
            if (!isset($company['Symbol'])) {
                continue;
            }
            if (!isset($company['Company Name'])) {
                continue;
            }
            // prepare for ChoiceType field
            $companySymbol[$company['Company Name']] = $company['Symbol'];
        }

        return $companySymbol;
    }

    public final function fetchCompanyName(string $companySymbol): string
    {
        $companySymbols = $this->fetchCompanySymbolInformation();

        if (empty($companySymbols)) {
            return '';
        }

        foreach ($companySymbols as $key => $item) {
            if ($item === $companySymbol) {
                return $key;
            }
        }

        return '';
    }

    public final function filterTableInformation(
        array $historicalData,
        CompanyHistory $companyHistoryFormData,
        string $dateFormat
    ): array
    {
        $tableData = array();

        if (empty($historicalData)) {
            return $tableData;
        }

        foreach ($historicalData as $company) {
            if (!isset($company['date'])) {
                continue;
            }

            if (!isset($company['open'])) {
                continue;
            }

            if (!isset($company['high'])) {
                continue;
            }

            if (!isset($company['low'])) {
                continue;
            }

            if (!isset($company['close'])) {
                continue;
            }

            if (!isset($company['volume'])) {
                continue;
            }

            if ($company['date'] < $companyHistoryFormData->getStartDate()->getTimestamp()) {
                continue;
            }

            if ($company['date'] > $companyHistoryFormData->getEndDate()->getTimestamp()) {
                continue;
            }

            // prepare display for table
            $tableData[] = [
                'date' => date($dateFormat, $company['date']),
                'open' => round($company['open'], self::DECIMAL_PRECISION),
                'close' => round($company['close'], self::DECIMAL_PRECISION),
                'high' => round($company['high'], self::DECIMAL_PRECISION),
                'low' => round($company['low'], self::DECIMAL_PRECISION),
                'volume' => $company['volume']
            ];
        }

        return $tableData;
    }

    public final function fetchChartInformation(array $historicalData): array
    {
        $chartData = array();

        if (empty($historicalData)) {
            return $chartData;
        }

        foreach ($historicalData as $company) {
            if (!isset($company['date'])) {
                continue;
            }

            if (!isset($company['open'])) {
                continue;
            }

            if (!isset($company['close'])) {
                continue;
            }

            // prepare structure for chart
            $chartData[] = [
                'date' => $company['date'],
                'open' => $company['open'],
                'close' => $company['close'],
            ];
        }

        return $chartData;
    }

    private function fetchCompanyInformation(): array
    {
        return $this->cache->get('company_information_response', function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $response = $this->client->request('GET', self::COMPANY_INFO_REQUEST_URL);
            $content = $response->toArray();

            return $content;
        });
    }
}
