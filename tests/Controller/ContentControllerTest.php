<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContentControllerTest extends WebTestCase
{
    const FORM_ROUTE = '/form';
    const FORM_TITLE = 'Please select';

    public function testCompanyHistoryForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORM_ROUTE);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', self::FORM_TITLE);
    }
}
