<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userName', TextType::class, array(
            'required' => true,
            'constraints' => array(new NotBlank())
        ))
        ->add('email', EmailType::class, array(
            'required' => true,
            'constraints' => array(new Email())
        ))
        ->add('password', PasswordType::class, array(
            'required' => true,
            'constraints' => array(new NotBlank())
        ))
        ->add('file', FileType::class, array(
            'required' => false
        ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'constraints' => array (
                new UniqueEntity(array('fields' => array('userName'))),
                new UniqueEntity(array('fields' => array('email')))
            )
        ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_registration';
    }

}
