<?php

namespace App\Form;

use App\Entity\EventType;
use App\Entity\Place;
use App\Form\CustomColorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PlaceType extends AbstractType
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
         $this->user = $tokenStorage->getToken()->getUser();        
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $super_admin = false;

        if ($this->user->hasRole('ROLE_SUPER_ADMIN')){
            $super_admin = true;
        }
        
        $builder->add('name',TextType::class, array(
                        'label' => 'Nom *'
                    )
                )
                ->add('nameMobile',TextType::class, array(
                        'label' => 'nom pour mobile/appli',
                        'required' => false
                    )
                )
                ->add('slug',TextType::class, array(
                        'label' => 'slug',
                        'required' => false
                    )
                )
                ->add('address',TextType::class, array(
                        'label' => 'Adresse *'
                    )
                )
                ->add('cp',TextType::class, array(
                        'label' => 'CP *'
                    )
                )
                ->add('city',TextType::class, array(
                        'label' => 'Ville *'
                    )
                )
                ->add('latitude',TextType::class, array(
                        'label' => 'Latitude', 
                        'required' => false,
                        'empty_data' => "0", 

                    )
                )
                ->add('longitude',TextType::class, array(
                        'label' => 'Longitude',
                        'required' => false,
                        'empty_data' => "0",                     

                    )
                )
                ->add('description',TextareaType::class, array(
                        'label' => 'Texte SEO de la page ville',
                        'required' => false
                    )
                )
                ->add('colors', CollectionType::class,
                    array(
                        'entry_type' => CustomColorType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
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
            'data_class' => Place::class,
            'slug' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'place';
    }
}
