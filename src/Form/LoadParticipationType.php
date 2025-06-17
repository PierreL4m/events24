<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\ParticipationCompanySimple;
use App\Entity\ParticipationDefault;
use App\Entity\ParticipationFormationSimple;
use App\Entity\ParticipationJobs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoadParticipationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $participation = $options['data'];
        $id = $participation->getId();
        $o_id = $participation->getOrganization()->getId();

        $builder->add('participation', EntityType::class, array(
                        'class' => get_class($participation),  
                        'choice_label' => function ($participation) {
                            return $participation->getEvent();
                        },
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($id, $o_id) {
                            $qb = $er->createQueryBuilder('p');
                            $qb->join('p.organization', 'o')
                                ->join('p.event', 'e')
                                ->where('o.id = :o_id')
                                ->andWhere('p.id != :id')
                                ->setParameters(array('id' => $id, 'o_id' => $o_id))
                                ->orderBy('e.date', 'DESC');

                            return $qb;
                        },
                        'expanded' => false,
                        'multiple' => false,
                        'label' => 'Choisir la participation à charger'
                    )                   
                )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Participation::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'load_participation';
    }
}
