<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\RecruitmentOffice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddRoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
     {        
        $builder->add('recruitmentOffices', EntityType::class, array(
            'class' => RecruitmentOffice::class,
            // 'query_builder' => function (EntityRepository $er) use ($event) {
            //     $qb = $er->createQueryBuilder('p')
            //         ->join('p.event', 'e')
            //         ->where('e.id = :e_id')
            //         ->setParameter('e_id',$event->getId());

            //     return $qb;
            // },
            'expanded' => true,
            'multiple' => true,
            'label' => 'Ajouter un cabinet de recrument existant',
            'by_reference' => false
        ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class
        ]);
    }
}
