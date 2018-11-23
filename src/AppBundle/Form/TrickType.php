<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'required' => true,
            'constraints' => array(new NotBlank())
        ))
        ->add('description', TextareaType::class, array(
            'required' => true,
            'constraints' => array(new Length(array('min' => 50)))
        ))
        ->add('category', TextType::class, array(
            'required' => true,
            'constraints' => array(new NotBlank())
        ))
        ->add('pictures', CollectionType::class, array(
            'entry_type' => PictureType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false
        ))
        ->add('videos', CollectionType::class, array(
            'entry_type' => VideoType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'by_reference' => false
        ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Trick',
            'constraints' => array (new UniqueEntity(
                    array('fields' => array('name'))
                    ))
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_trick';
    }

}
