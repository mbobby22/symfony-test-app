<?php

namespace App\Form\Type;

use App\Service\CompanyService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\CompanyHistory;
use Symfony\Component\Translation\TranslatableMessage;

class CompanyHistoryType extends AbstractType
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companySymbol', ChoiceType::class, [
                'placeholder' => new TranslatableMessage('form.label.choice'),
                'choices'  => $this->companyService->fetchCompanySymbolInformation(),
            ])
            ->add('startDate', DateType::class)
            ->add('endDate', DateType::class)
            ->add('email', EmailType::class)
            ->add('save', SubmitType::class, ['label' => new TranslatableMessage('form.label.submit')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyHistory::class,
        ]);
    }
}
