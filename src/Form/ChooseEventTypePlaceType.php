<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\EventType;

class ChooseEventTypePlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            
        $builder->add('type', EntityType::class, array(
            'class' => EventType::class,
            'choice_label' => function ($type) {
                return $type->getFullName();
            },
            'label' => 'Type d\'événement'
            )
        )
        ;
        
        $builder->add('place', EntityType::class, array(
                        'class' => Place::class,  
                        'choice_label' => function ($place) {
                            return $place->getName().$place->getCity();
                        },
                        'label' => 'Lieu'
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
            'data_class' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'choose_place';
    }
}
