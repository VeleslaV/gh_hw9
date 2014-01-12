<?php

namespace Veles\HomeWorkBundle\Tests\Controller;

use Veles\HomeWorkBundle\Controller\AjaxController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase {

    public function testAjaxLoadMoreArticle()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hw/ajax/load_articles/2/', array(), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Category")')->count()
        );
    }
}
 