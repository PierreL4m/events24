<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEventsToPartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']->getId();
       
        $builder->add('events', EntityType::class, array(
            'class' => Event::class,
            'query_builder' => function (EntityRepository $er) {
                $qb2 = $er->createQueryBuilder('e2')
                ->select('e2.id')
                ->join('e2.partners','p2')
                ->getDql();

                $qb = $er->createQueryBuilder('e');

                return $qb
                    //->where($qb->expr()->notIn('e.id',$qb2))
                    ->where('e.date > :now')
                    ->setParameters(array('now' => new \DateTime()))
                    ->orderBy('e.date', 'DESC')
                    ;
            },
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'by_reference' => false
            )            
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partner::class           
        ]);
    }
}


