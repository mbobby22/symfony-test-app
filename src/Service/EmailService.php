<?php

namespace App\Service;

use Symfony\Component\Mime\Email;

class EmailService extends AbstractEmailService
{
    public function send(array $data): bool
    {
        $email = (new Email())
            ->from($this->params->get('app.email.from'))
            ->to($data['to'])
            ->subject($data['subject'])
            ->text('From ' . $data['start_date'] . ' to ' . $data['end_date']);

        $this->mailer->send($email);

        return true;
    }
}
