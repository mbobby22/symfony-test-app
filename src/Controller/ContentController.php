<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CompanyHistory;
use App\Form\Type\CompanyHistoryType;
use App\Service\EmailService;
use App\Service\CompanyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ContentController extends AbstractController
{
    const FORM_ROUTE = '/form';
    const DATETIME_FORMAT = 'Y-m-d';
    const CHART_LINE_COLOR_GREEN = 'rgb(50,205,50)';
    const CHART_LINE_COLOR_RED = 'rgb(220,20,60)';

    #[Route(self::FORM_ROUTE)]
    public function form(
        Request $request,
        TranslatorInterface $translator,
        EmailService $mailer,
        CompanyService $companyService,
        ChartBuilderInterface $chartBuilder
    ): Response
    {
        $companyHistory = new CompanyHistory();

        $form = $this->createForm(CompanyHistoryType::class, $companyHistory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            $companyHistoryFormData = $form->getData();
            // send notification
            $data = $this->prepareEmailData($companyHistoryFormData, $companyService);
            $sendEmail = $mailer->send($data);
            // get raw table data
            $historicalData = $companyService->fetchHistoricalData($companyHistoryFormData->getCompanySymbol());
            // filter items
            $filteredHistoricalData = $companyService->filterTableInformation(
                $historicalData,
                $companyHistoryFormData,
                self::DATETIME_FORMAT
            );
            // show chart
            $chart = $this->generateChart(
                $filteredHistoricalData,
                $chartBuilder,
                $companyService,
                $translator
            );

            return $this->renderForm('company-history/form.html.twig', [
                'form' => $form,
                'title' => $translator->trans('form.label.select'),
                'result' => $sendEmail,
                'data' => $filteredHistoricalData,
                'chart' => $chart,
           ]);
        }

        return $this->renderForm('company-history/form.html.twig', [
            'form' => $form,
            'title' => $translator->trans('form.label.select'),
            'data' => null,
            'chart' => null,
        ]);
    }

    private function generateChart(
        array $tableData,
        ChartBuilderInterface $chartBuilder,
        CompanyService $companyService,
        TranslatorInterface $translator,
    ): Chart
    {
        $chartData = $companyService->fetchChartInformation($tableData);
        $chartDates = array_column($chartData, 'date');
        $chartOpenPrices = array_column($chartData, 'open');
        $chartClosePrices = array_column($chartData, 'close');

        $companyChart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $companyChart->setData([
            'labels' => $chartDates,
            'datasets' => [
                [
                    'label' => $translator->trans('table.head.open'),
                    'backgroundColor' => self::CHART_LINE_COLOR_GREEN,
                    'borderColor' => self::CHART_LINE_COLOR_GREEN,
                    'data' => $chartOpenPrices,
                ],
                [
                    'label' => $translator->trans('table.head.close'),
                    'backgroundColor' => self::CHART_LINE_COLOR_RED,
                    'borderColor' => self::CHART_LINE_COLOR_RED,
                    'data' => $chartClosePrices,
                ],
            ],
        ]);

        $companyChart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => min(min($chartOpenPrices), min($chartClosePrices)),
                    'suggestedMax' => max(max($chartClosePrices), max($chartClosePrices)),
                ],
            ],
        ]);

        return $companyChart;
    }

    private function prepareEmailData(CompanyHistory $companyHistoryFormData, CompanyService $companyService): array
    {
        $companyName = $companyService->fetchCompanyName(
            $companyHistoryFormData->getCompanySymbol()
        );

        return [
            'to' => $companyHistoryFormData->getEmail(),
            'subject' => $companyName,
            'start_date' => $companyHistoryFormData->getStartDate()->format(self::DATETIME_FORMAT),
            'end_date' => $companyHistoryFormData->getEndDate()->format(self::DATETIME_FORMAT),
        ];
    }
}
