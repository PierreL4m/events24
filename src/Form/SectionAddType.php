<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\SectionType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = $options['data'];

        $builder->add('section_types', EntityType::class, array(
            'class' => SectionType::class,
            'query_builder' => function (EntityRepository $er) use ($event) {
                $qb2 = $er->createQueryBuilder('st2')
                    ->select('st2.id')
                    ->join('st2.sections', 's2')
                    ->join('s2.event', 'e2')
                    ->where('e2.id = :id')
                    ->getDql();

                $qb = $er->createQueryBuilder('st');
                    

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
