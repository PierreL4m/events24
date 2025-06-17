<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\Job;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use addCustomDatetimeFunction;

class ImportJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $participation = $options['data'];

        $builder->add('jobs', EntityType::class, array(
            'class' => Job::class,
            'query_builder' => function (JobRepository $repo) use ($participation){
                $job = $repo->getByOrganizationAndYear($participation);
                return $job;
            },
            'expanded' => true,
            'multiple' => true,
            'choice_label' => function ($job) {
                $label = $job->getName().' - '.$job->getParticipation()->getEvent()->getPlace()->getCity().' '.date_format($job->getParticipation()->getEvent()->getDate(),'Y') ;
                return $label;
            },
            'mapped' => false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}
