<?php

namespace GB\TricksBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form components
 */
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class)
                ->add('description', TextareaType::class)
                ->add('category', TextType::class)
                ->add('pictures', CollectionType::class, 
                        array('entry_type' => PictureType::class,
                            'allow_add' => TRUE,
                            'allow_delete' => TRUE,
                            'required' => FALSE,
                            'by_reference' => FALSE))
                ->add('videos', CollectionType::class,
                        array('entry_type' => VideoType::class,
                            'allow_add' => TRUE,
                            'allow_delete' => TRUE,
                            'required' => FALSE,
                            'by_reference' => FALSE))
                ->add('save', SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'GB\TricksBundle\Entity\Trick',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gb_tricksbundle_trick';
    }


}
