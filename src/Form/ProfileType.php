<?php

namespace App\Form;


use App\Entity\User;
use App\Helper\TwigHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    private $helper;

    public function __construct(TwigHelper $helper)
    {
        $this->helper = $helper;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data'];

        if ($this->helper->isAtLeastViewer($user)){
            $builder->add('username',TextType::class, array(
                    'label' => 'Nom d\'utilisateur *',
                )
            );
        }
        $builder
           
           ->add('firstname',TextType::class, array(
                    'label' => 'Prénom *',
                )
            )
            ->add('lastname',TextType::class, array(
                    'label' => 'Nom *',
                )
            )
            ->add('email',EmailType::class, array(
                    'label' => 'Email *',
                )
            )           
            ->add('phone',TextType::class, array(
                    'label' => 'Téléphone *',
                )
            )
            ->add('position',TextType::class, array(
                    'label' => 'Fonction *',
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
