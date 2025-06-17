<?php

namespace App\Form;

use App\Entity\Email;
use App\Entity\Participation;
use App\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $email = $options['data'];
        $event_id = $email->getEvent()->getId();
        $email_type = $email->getEmailType()->getSlug();
        $accepted_format = $email->getAcceptedFormat();
        $builder
            ->add('subject', TextType::class,array(
                'label' => 'Sujet *'
                )
            )
            ->add('body', TextareaType::class,array(
                'label' => 'Corps du mail *',
                'required' => false
                )
            )
        ;

        switch ($email_type) {
            case 'partners':
               $builder->add('recipients', EntityType::class, array(
                    'class' => Partner::class,
                    'query_builder' => function (EntityRepository $er) use ($event_id) {

                        return $er->createQueryBuilder('p')
                            ->join('p.events', 'e')
                            ->where('e.id = :id')
                            ->andWhere('p.email is not null')
                            ->setParameter('id',$event_id)
                            ->orderBy('p.name', 'ASC');
                    },
                    'expanded' => true,
                    'multiple' => true,
                    'choice_label' =>  function ($partner) {

                        $label = $partner->getName().' - '.$partner->getEmail();

                        return $label;
                    },
                    'label' => 'Destinataires',
                    'mapped' => false,
                    'choice_attr' => function ($val, $key, $index) {
                       return array('checked' => true);
                    }
                ));
                break;

            default:
                $builder->add('recipients', EntityType::class, array(
                    'class' => Participation::class,
                    'query_builder' => function (EntityRepository $er) use ($event_id) {

                        return $er->createQueryBuilder('p')
                            ->join('p.event', 'e')
                            ->where('e.id = :id')
                            ->andWhere('p.responsable is not null')
                            ->setParameter('id',$event_id)
                            ->orderBy('p.companyName', 'ASC');
                    },
                    'expanded' => true,
                    'multiple' => true,
                    'choice_label' =>  function ($participation) {

                        $label = $participation->getCompanyName().' - '.$participation->getResponsable()->getEmail();

                        if (method_exists($participation->getResponsable(),'getResponsableBises')){
                            foreach ($participation->getResponsable()->getResponsableBises() as $bis) {
                                $label = $label.' / '.$bis->getEmail();
                            }
                        }

                        return $label;
                    },
                    'label' => 'Destinataires',
                    'mapped' => false,
                    'choice_attr' => function ($val, $key, $index) {
                       return array('checked' => true);
                    }
                ));
                // throw new \Exception('No EmailType slug="'.$email_type.'" found to construct MailTypeForm');
                break;
        }

        $builder->add('extra_mails', CollectionType::class, array(
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label'=> 'Destinataires supplémentaires',
                    'required' => false,
                    'mapped' => false,
                    'entry_options' => array(
                        'label' => false
                    )
                )
            )
            ->add('attachmentFile',
                FileType::class,
                array(
                    'label' => 'Pièce Jointe (max 1024KB (1MB))',
                    'required' => false,
                    'constraints' => array(
                        new File(
                            array (
                                'maxSize' => '1100K',
                                'mimeTypes' => $accepted_format
                            )
                        )
                    )
                )
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Email::class,
        ]);
    }
}
