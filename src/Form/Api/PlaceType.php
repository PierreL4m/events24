<?php

namespace App\Form\Api;

use App\Entity\Place;
use App\Form\Api\CustomColorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
        
                ->add('address')
                ->add('cp')
                ->add('city')
                ->add('description')
                ->add('colors', CollectionType::class,
                    array(
                        'entry_type' => CustomColorType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                    )
                )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Place::class,
            'slug' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'place';
    }
}
