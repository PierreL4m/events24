<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Section;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionRemoveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = $options['data'];

        $builder->add('sections', EntityType::class, array(
            'class' => Section::class,
            'query_builder' => function (EntityRepository $er) use ($event) {

                return $er->createQueryBuilder('s')
                        ->join('s.event','e')
                        ->where('e.id = :id')
                        ->setParameter('id', $event->getId())
                        ->orderBy('s.sOrder', 'ASC');
            },
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'mapped' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
