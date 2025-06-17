<?php

namespace App\Form;

use App\Entity\SectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionTypeChangeOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('sectionType', CollectionType::class,
                    array(
                        'entry_type' => SectionTypeSetOrderType::class, 
                        'label' => false,
                        'data' => $options['section_types'],
                        'entry_options' => array('label' => false)
                        )
                    )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'section_types' => null
        ]);
    }
}
