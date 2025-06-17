<?php

namespace App\Form;

use App\Entity\BilanPicture;
use App\Entity\Participation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class BilanPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files',FileType::class, array( 
                'label'=>'Fichiers JPG uniquement (30Mo max au total)',
                'multiple' => true,
                'constraints' => [
                    new All(
                        array(
                             new File([
                                'maxSize' => '50M',
                                'mimeTypes' => 'image/jpeg',
                                'mimeTypesMessage' => 'Merci de charger un fichier JPG valide'
                                ])
                        )
                    )
                ],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
