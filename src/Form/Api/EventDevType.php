<?php

namespace App\Form\Api;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventDevType extends AbstractType
{

    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
           

            $builder->add('online', TextType::class, [
                'documentation' => [
                    'type' => 'date format dmYHi',
                    'description' => 'online_date(show in event list page) format dmYHi',
                    ]
                ]
            )
            ->add('offline', TextType::class, [
                'documentation' => [
                    'type' => 'date format dmYHi',
                    'description' => 'offline_date(not shown in event list page) format dmYHi',
                    ]
                ]
            )
            
            ->add('date', TextType::class, [
                'documentation' => [
                    'type' => 'date format dmYHi',
                    'event_date(exposant scan has to be done on today\'s event) format dmYHi',
                    ]
                ]
            )
            
        ;
      
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false
        ]);
    }
}
