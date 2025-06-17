<?php

namespace App\Form;

use App\Entity\EventJobs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Status;
use App\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\UserRepository;
use App\Entity\Sector;

class SearchCandidateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $builder->add('text', TextType::class, array('label' => false, 'required' => false, 'attr' => ['placeholder' => 'Recherche par email/nom/prénom/téléphone']));
        
        
                
       if(!empty($options['event'])) {
           $event = $options['event'];
           
           $builder->add('user', EntityType::class,
               array(
                   'class' => User::class,
                   'query_builder' => function(UserRepository $er) use ($event) {
                       return $er->queryAllowedAdminsByEvent($event);
                   },
                   'expanded' => false,
                   'multiple' => true,
                   'label' => 'Suivi par',
                   'required' => false,
                   'placeholder' => 'Tout le monde',
                   )
               );
           
           if(!$event->getType()->registrationValidationAuto()) {
               
               $builder->add('status', EntityType::class,
                   array(
                       'class' => Status::class,
                       'expanded' => true,
                       'multiple' => true,
                       'label' => false
                   )
                   );
               
               
               if ($event instanceof EventJobs) {
                   $builder->add('rhSectors', EntityType::class,
                       array(
                           'class' => Sector::class,
                           'choices' => $event->getSectors(),
                           'expanded' => true,
                           'multiple' => true,
                           'label' => false,
                           'required' => false
                       )
                   );
               }
           }
           $builder->add('came', CheckboxType::class,array('label' => 'Venus','required' => false));
       }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'event' => null,
        ));
    }
}
