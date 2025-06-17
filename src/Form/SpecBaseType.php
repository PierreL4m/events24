<?php

namespace App\Form;

use App\Entity\SpecBase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SpecBaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, array(
                'label' => 'Nom de la base de cahier des charges*',
                )
            )
            // ->add('default')
            ->add('file', FileType::class,array(
                    'label' => 'Base du cahier des charges sans la dernière page en PDF',
                    'constraints' => array(
                        new File(
                            array (
                                'maxSize' => '2000K',
                                'mimeTypes' => array('application/pdf', 'application/xpdf')
                            )
                        )
                    ),
                    'mapped' => false,
                    'required' => false
                )
            )
        ;
            
        if($options['event']) {
            if($options['event']->getSpecBase()->isUseDefault()) {
                $builder->add('editDefault', CheckboxType::class,
                    array(
                        'label' => "Modifier le cahier des charges par défaut (pour TOUS les événements qui n'ont pas leur propre cahier des charges)",
                        'required' => false,
                        'mapped' => false,
                        'data' => true
                    )
                );
            }
            else {
                $builder->add('useDefault', CheckboxType::class,
                    array(
                        'label' => "Utiliser plutôt le cahier des charges par défaut",
                        'required' => false,
                        'mapped' => false,
                        'data' => false
                    )
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SpecBase::class,
            'event' => null
        ]);
    }
}
