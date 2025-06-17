<?php

namespace App\Form;

use App\Entity\BilanFile;
use App\Entity\BilanFileType as Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BilanFileTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('name', TextType::class, array('label' => 'Nom du fichier *'))
        ;

        //$bilanFileType = ->getBilanFileType();
        $type = $options['data']->getBilanFileType()->getType();
        
        if($type == 'youtube' || $type == 'iframe'){
            $builder->add('url');
        }
        else{
            switch ($type) {
                case 'mp3':
                    $mimeTypes = ['audio/mpeg3',"audio/x-mpeg-3", "audio/mpeg","application/octet-stream"];
                    break;
                case 'mp4':
                    $mimeTypes = ["video/mpeg", "application/octet-stream","video/mp4","video/x-m4v"];
                    break;
                case 'pdf':
                    $mimeTypes = ['application/pdf','application/x-pdf'];
                    break;
                
                default:
                    throw new \Exception('Le type de fichier '.$type.' n\'est pas encore pris en charge');
                    break;
            }
            $builder->add('file', FileType::class, array(
                'label' => 'Fichier '.$type.' (max 100Mo)',
                 'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => $mimeTypes,
                        'mimeTypesMessage' => 'Merci de charger un fichier '.$type.' valide',
                    ])
                ],
                'required' => $options['required']
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BilanFile::class,
            'required' => true
        ]);
    }
}
