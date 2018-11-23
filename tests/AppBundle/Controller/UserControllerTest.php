<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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
        $this->assertTrue(!($client->getResponse()->isSuccessful()));
    }
    
    /**
     * @dataProvider provideAdminUrls
     */
    public function testAdminPageIsSuccessful($url)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'login');
        $form = $crawler->selectButton('Connexion')->form();
        $form->setValues(array(
            '_username' => 'snowtrickstest',
            '_password'   => 'timpig!1',
        ));
        $client->submit($form);
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return array(
            array(
                '/login',
                '/registration',
                '/forgotpassword'
            )
        );
    }
    
    public function provideAdminUrls()
    {
        return array(
            array(
                '/user',
                '/user/update'
            )
        );
    }
}
