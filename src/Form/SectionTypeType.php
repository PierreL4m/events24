<?php

namespace App\Form;

use App\Entity\Section;
use App\Entity\SectionType;
use App\Helper\GlobalHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SectionTypeType extends AbstractType
{
    private $user;
    private $helper;

    public function __construct(TokenStorageInterface $tokenStorage, GlobalHelper $helper)
    {
         $this->user = $tokenStorage->getToken()->getUser();
         $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->user->hasRole('ROLE_SUPER_ADMIN')){
            $builder->add('slug', TextType::class, array(
                    'required' => false,
                ))
            ;
        }

        $builder
            ->add('title', TextType::class, array(
                'label' => 'Titre'
            ))
            ->add('menuTitle', TextType::class, array(
                'label' => 'Titre dans le menu',
                'attr' => array('placeholder' => 'Par défaut le titre'),
                'required' => false
            ))
            ->add('apiTitle', TextType::class, array(
                'label' => 'Titre sur l\'appli',
                'attr' => array('placeholder' => 'Par défaut le titre'),
                'required' => false
            ))
            ->add('automaticGeneration', CheckboxType::class, array(
                'label' => 'Chargement des données précédentes ',
                'required' => false
            ))
            ->add('defaultOnPublic', CheckboxType::class, array(
                'label' => 'Présence par défaut sur le site public ',
                'required' => false
            ))
            ->add('defaultOnCity', CheckboxType::class, array(
                'label' => 'Présence par défaut sur la page ville',
                'required' => false
            ))
        ;
        if($options['new']){
            $builder->add('sectionClass', ChoiceType::class,array(
                    'choices' => [
                        'Simple = une image et/ou du texte' => 'SectionSimple',
                        'Avec logo = une image et/ou du texte plus une liste de logo (ex rubrique village du kinépolis)' => 'SectionParticipant',
                        'Agenda = avec planin + logo et description' => 'SectionAgenda'
                    ],
                    'label' => 'Affichage de la rubrique'
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SectionType::class,
            'new' => null
        ]);
    }
}
