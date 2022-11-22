<?php

namespace App\Tests\Service;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailServiceTest extends KernelTestCase
{
    public function testSendEmail()
    {
        // setup test data
        $data = [
            'to' => 'john@doe.com',
            'subject' => 'GOOGL',
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-31',
        ];

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $emailServiceMock = $container->get(EmailService::class);
        $sendEmail = $emailServiceMock->send($data);

        $this->assertEquals('1', $sendEmail);

        $email = $this->getMailerMessage();
        $this->assertEmailTextBodyContains($email, 'From');
    }
}
