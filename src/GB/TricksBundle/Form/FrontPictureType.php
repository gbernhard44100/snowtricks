<?php

namespace GB\TricksBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrontPictureType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $NbPicture = $options['data']->getPictures()->count();
        $array = array_combine(range(1, $NbPicture),range(1, $NbPicture));

        $builder->add('frontImage', ChoiceType::class, array(
            'choices' =>  $array));
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
        return 'gb_tricksbundle_picture';
    }

}
