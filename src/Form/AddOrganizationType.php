<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Organization;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddOrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = $builder->getData();
        
        $builder->add('organizations', EntityType::class, array(
            'class' => Organization::class,
            'query_builder' => function (EntityRepository $er) use ($event) {
                $qb = $er->createQueryBuilder('o');
                $qb2 = $er->createQueryBuilder('o2')
                       ->select('o2.id')
                       ->join('o2.organizationTypes' ,'ot');
                $i=0;

                foreach ($event->getOrganizationTypes() as $type) {
                    $qb2->orWhere('ot.id = :id'.$i);
                    $qb->setParameter('id'.$i, $type->getId());
                    $i++;
                }

                $qb2 = $qb2->getDql();

                $qb3 = $er->createQueryBuilder('o3')
                        ->join('o3.participations', 'p3')
                        ->join('p3.event', 'e3')
                        ->where('e3.id = :e_id');

                $qb->setParameter('e_id',$event->getId());
                $qb->where($qb->expr()->in('o.id',$qb2))
                    ->andWhere($qb->expr()->notin('o.id',$qb3->getDql()))
                    ->orderBy('o.name', 'ASC');

                return $qb;
            },
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'choice_label' =>  function ($organization) {
                $label = $organization->getName();

                 
                foreach ($organization->getOrganizationTypes() as $type){
                    $label =$label." - ".$type->getName();
                }
              

                return $label;
            },
            'mapped' => false
            )            
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}


