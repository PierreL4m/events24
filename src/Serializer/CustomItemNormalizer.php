<?php
namespace App\Serializer;

use App\Model\ApiForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Form;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Exception\CustomApiException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use App\Exception\CustomApiFormException;

final class CustomItemNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private $decorated;

    private $normalizable = array(
        CustomApiException::class,
        CustomApiFormException::class
    );

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null)
    {
        if(is_object($data) && ($data instanceof FlattenException || $data instanceof ApiForm)) {
            return true;
        }
        return $this->decorated->supportsNormalization($data, $format);
    }

    private function normalizeForm(Form $object, $format = null, array $context = [])
    {

        $data = $object->all();
        $form = array();
        $map = array(
            EntityType::class => 'select',
            TextType::class => 'text',
            EmailType::class => 'email',
            TextareaType::class => 'textarea',
            ChoiceType::class => 'checkbox',
            FileType::class => 'file',
            PasswordType::class => 'password',
        );
        foreach ($data as $d){
            $tab = array('label' => $d->getConfig()->getOption("label"),'name' => $d->getName(),'fieldType' => $map[get_class($d->getConfig()->getType()->getInnerType())],'required' => $d->isRequired(), 'choices' => $d->getConfig()->getAttributes());
            $tab['required'] != false ? $form[]  = $tab : null;
        }
        return $form;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        if($object instanceof ApiForm){
            return $this->normalizeForm($object->getForm(), $format, $context);
        }
        $data = $this->decorated->normalize($object, $format, $context);


        if($object instanceof FlattenException && in_array($object->getClass(), $this->normalizable)) {
            if (is_array($data)) {
                $decoded = json_decode($object->getMessage(), true);
                $data['code'] = $object->getStatusCode();
                $data['message'] = $decoded['message'];
                $data['errors'] = $decoded['errors'];
                // children exist only for form errors
                if(!empty($data['errors']) && !empty($data['errors']['children'])) {
                    foreach($data['errors']['children'] as $k => $child) {
                        $data['errors']['children'][$k] = new \stdClass();
                        if(empty($child['errors']) || count($child['errors']) < 1) {

                        }
                        else {
                            $errors = [];
                            foreach($child['errors'] as $t) {
                                if(is_object($t)) {
                                    $errors[] = $t->message;
                                }
                                elseif(is_array($t)) {
                                    $errors[] = $t['message'];
                                }
                                elseif(is_string($t)) {
                                    $errors[] = $t;
                                }
                            }
                            $data['errors']['children'][$k]->errors = $errors;
                        }
                    }
                }
                unset($data['trace'], $data['title'], $data['detail'], $data['type']);
            }
        }

        return $data;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}