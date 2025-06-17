<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\StandType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StandTypeParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stand_type = $options['data'];
      
        $builder
            ->add('participations', EntityType::class, array(
                'class' => Participation::class,
                'label'=> false,
                'choice_label' => 'companyName',
                'query_builder' => function (EntityRepository $er) use ($stand_type) {
                    return $er->createQueryBuilder('p')
                        ->join('p.event','e')
                        ->leftjoin('p.standType', 's')
                        
                        ->where('p.standType is NULL')
                        ->orWhere('s.dimension = :dimension')
                        ->andWhere('e.id = :id')
                        ->setParameters(array('id' => $stand_type->getTechGuide()->getEvent()->getId(), 'dimension' => $stand_type->getDimension()))
                        ->orderBy('p.companyName', 'ASC');
                },
                'expanded' => true,
                'multiple' => true,
                'by_reference' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StandType::class,
        ]);
    }
}
