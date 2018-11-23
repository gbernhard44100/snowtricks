<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    
    public function testAddTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $crawler = $client->request('GET', '/add');
        $form = $crawler->selectButton('save')->form();
        $client->submit($form, array(
            'appbundle_trick[name]' => 'TrickTest',
            'appbundle_trick[category]' => 'Flip',
            'appbundle_trick[description]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing volutpat.',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/trick/12');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testModifyTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $crawler = $client->request('GET', '/modify/3');
        $form = $crawler->selectButton('save')->form();
        $photo = new UploadedFile(
            '/var/www/html/snowtricks/web/pictures/background.jpeg',
            'testpicture.jpeg',
            'image/jpeg'
        );
        $client->submit($form, array(
            'appbundle_trick[name]' => 'Modified TrickTest',
            'appbundle_trick[category]' => 'Slide',
            'appbundle_trick[description]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing volutpat.',
            'appbundle_trick[pictures][0][file]' => $photo,
            'appbundle_trick[videos][0][url]' => 'https://www.youtube.com/embed/iKkhKekZNQ8'
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/trick/3');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testModifyFrontPicture()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $crawler = $client->request('GET', '/trick/8');
        $form = $crawler->selectButton('Save')->form();
        $form['appbundle_front_picture[frontImage]']->select(2);
        $client->submit($form);
        $client->followRedirects();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $form = $crawler->selectButton('Reset')->form();
        $client->submit($form);
        $client->followRedirects();
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

    public function testMessageOTrick()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'snowtrickstest',
            'PHP_AUTH_PW'   => 'timpig!1',
        ));
        $crawler = $client->request('GET', '/trick/4');
        $form = $crawler->selectButton('send')->form();
        $client->submit($form, array(
            'appbundle_message[content]' => 'J\'écris un message pour tester.',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/trick/4');
        $this->assertCount(1, $crawler->filter('div.media.comment'));
        $crawler = $crawler->filter('div.media.comment')->first();
        $this->assertCount(1, $crawler->selectImage('photo de profil par défaut')->images());
    }

    public function provideUrls()
    {
        return array(
            array(
                '/',  
            ),
            array(
                '/trick/11',
            )
        );
    }
    
    public function provideAdminUrls()
    {
        return array(
            array(
                '/add',
            ),
            array(
                '/modify/10',
            )
        );
    }
}
