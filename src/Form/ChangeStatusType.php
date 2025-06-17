<?php

namespace App\Form;

use App\Entity\CandidateParticipation;
use App\Entity\Status;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $qb = null;

        if(!empty($options['data'])) {
            $cp = $options['data'];
            if($cp->getStatus()) {
                $qb = function (EntityRepository $er) use ($cp) {
                    return $er->createQueryBuilder('s')
                        ->where('s.id != :sid')
                        ->setParameter('sid', $cp->getStatus()->getId());
                };
            }
        }

        
        $builder            
            ->add('status', EntityType::class, array(
                'label' => 'Changer le statut',
                'required' => true,
                'class' => Status::class,
                'choice_label' => function ($status) {
                            return ucfirst($status);
                        },
                'query_builder' => $qb
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidateParticipation::class,
        ]);
    }
}
