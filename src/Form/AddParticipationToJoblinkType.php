<?php

namespace App\Form;

use App\Entity\Joblink;
use App\Entity\ParticipationJobs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddParticipationToJoblinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['event_id'];
        $joblink_id = $options['data']->getId();

        $builder->add('participations', EntityType::class, array(
                'class' => ParticipationJobs::class,
                'query_builder' => function (EntityRepository $er) use ($id, $joblink_id) {
                    return $er->createQueryBuilder('p')
                        ->join('p.event', 'e')
                        // ->leftjoin('p.joblinkSessions','j')
                        ->where('e.id = :id')
                        // ->andWhere('j is null')
                        ->setParameters(array('id' => $id))
                        ->orderBy('p.companyName', 'ASC');
                },
                'expanded' => true,
                'multiple' => true,
                'label' => false,
                'by_reference' => false,
                'mapped' => false
            )
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Joblink::class,
            'event_id' => null
        ]);
    }
}
