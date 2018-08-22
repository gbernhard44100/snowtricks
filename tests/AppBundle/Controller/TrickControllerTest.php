<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider provideAdminUrls
     */
    public function testFirewallIsSuccessful($url)
    {
        $client = static::createClient();        
        $client->request('GET', $url);
        $this->assertFalse($client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider provideAdminUrls
     */
    public function testAdminPageIsSuccessful($url)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testDeleteTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $crawler = $client->request('GET', '/');
        $start = $crawler->filter('a.suppressTrick')->count();
        $link = $crawler->filter('a.suppressTrick')->eq(0)->link();
        $crawler = $client->click($link);
        $crawler = $client->followRedirects();
        $crawler = $client->request('GET', '/');
        $this->assertCount(($start - 1), $crawler->filter('a.suppressTrick'));
    }

    public function provideUrls()
    {
        return array(
            array(
                '/',
                '/trick/11'
            )
        );
    }
    
    public function provideAdminUrls()
    {
        return array(
            array(
                '/add',
                '/modify/11'
            )
        );
    }
}
