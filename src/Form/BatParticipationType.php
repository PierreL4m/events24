<?php

namespace App\Form;

use App\Entity\Bat;
use App\Entity\Participation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bat = $builder->getData();
        $event = $bat->getEvent();
        
        $builder->add('participations', EntityType::class, array(
            'class' => Participation::class,
            'query_builder' => function (EntityRepository $er) use ($event) {
                $qb = $er->createQueryBuilder('p')
                    ->join('p.event', 'e')
                    ->where('e.id = :e_id')
                    ->setParameter('e_id',$event->getId());

                return $qb;
            },
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'by_reference' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bat::class,
        ]);
    }
}


