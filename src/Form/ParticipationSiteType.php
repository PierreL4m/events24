<?php

namespace App\Form;

use App\Entity\ParticipationSite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationSiteType extends AbstractType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url',TextType::class, array(
                    'label' => 'Url *'
                )
            )
        ;
        
        if ($options['helper']->isAtLeastViewer($options['user'])){
            $builder->add('label',TextType::class, array(
                    'label' => 'Label sur le site public',
                    'required' => false
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ParticipationSite::class,
            'user' => null,
            'helper' => null
        ]);
    }
}
