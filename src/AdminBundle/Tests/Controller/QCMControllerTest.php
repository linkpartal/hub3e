<?php

namespace AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QCMControllerTest extends WebTestCase
{
    public function testLoad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/gestionQCM');
    }

}
