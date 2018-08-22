<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Video;
use AppBundle\Form\TrickType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrickTypeTest extends TypeTestCase
{
    private $validator;

    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $this->validator = $this->getMock(ValidatorInterface::class);
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));

        return array(
            new ValidatorExtension($this->validator),
        );
    }
    
    public function testSubmitValidData()
    {
        $formData = array(
            'name' => 'test',
            'description' => 'Une chaine de plus de 50 charactères : Nunc vero inanes flatus'
                . ' quorundam vile esse quicquid extra urbis pomerium nascitur aestimant praeter'
                . ' orbos et caelibes, nec credi potest qua obsequiorum diversitate coluntur homines'
                . ' sine liberis Romae.',
            'category' => 'flip',
            'videos' => array('https://www.youtube.com/embed/vf9Z05XY79A', 'https://www.youtube.com/embed/QX6yvs6uTVg')
        );

        $objectToCompare = new Trick();
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(TrickType::class, $objectToCompare);
        
        $object = new Trick();
        $object->setName('test');
        $object->setDescription(
            'Une chaine de plus de 50 charactères : Nunc vero inanes flatus'
                . ' quorundam vile esse quicquid extra urbis pomerium nascitur aestimant praeter'
                . ' orbos et caelibes, nec credi potest qua obsequiorum diversitate coluntur homines'
                . ' sine liberis Romae.'
        );
        $object->setCategory('flip');
        $video1 = new Video();
        $video1->setUrl('https://www.youtube.com/embed/vf9Z05XY79A');
        $video2 = new Video();
        $video2->setUrl('https://www.youtube.com/embed/QX6yvs6uTVg');
        
        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        
        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}

