<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class CompanyHistory extends AbstractEntity
{
    #[Assert\NotBlank]
    protected $companySymbol;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTime::class)]
    #[Assert\LessThan('today')]
    #[Assert\LessThan(null, 'endDate')]
    protected $startDate;

    #[Assert\NotBlank]
    #[Assert\Type(\DateTime::class)]
    #[Assert\LessThan('today')]
    #[Assert\GreaterThan(null, 'startDate')]
    protected $endDate;

    #[Assert\NotBlank]
    protected $email;

    public function getCompanySymbol(): string
    {
        return $this->companySymbol;
    }

    public function setCompanySymbol(string $companySymbol): void
    {
        $this->companySymbol = $companySymbol;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
