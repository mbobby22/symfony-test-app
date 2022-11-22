<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractEmailService extends AbstractService
{
    protected MailerInterface $mailer;

    abstract public function send(array $data): bool;

    public function __construct(
        ParameterBagInterface $parameterBag,
        MailerInterface $mailer
    ) {
        parent::__construct($parameterBag);

        $this->mailer = $mailer;
    }
}
