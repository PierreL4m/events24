<?php
namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\RequestStack;

final class SnakeCaseItemNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    /**
     * 
     * @var RequestStack
     */
    private RequestStack $stack;
    
    /**
     * 
     * @var NormalizerInterface
     */
    private NormalizerInterface $decorated;

    public function __construct(RequestStack $stack, NormalizerInterface $decorated)
    {
        $this->stack = $stack;
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decorated->supportsNormalization($data, $format);
    }
    
    private function clonePropertiesToSnakeCase($k, $v) :array {
        $result = [];
        if(is_array($v)) {
            $tmp = array();
            foreach($v as $k2 => $v2) {
                $d = $this->clonePropertiesToSnakeCase($k2, $v2);
                foreach($d as $k3 => $v3) {
                    $tmp[$k3] = $v3;
                }
            }
            $v = $tmp;
        }
        
        $result[$k] = $v;
        // key is in camel case
        if(preg_match('/[A-Z]/', $k)) {
            // convert it to snake case
            $tmp = preg_replace('/^_/', '', strtolower(preg_replace('/([A-Z])/', '_$1', $k)));
            $result[$tmp] = $v;
        }
        // key is in snake case
        elseif(false !== strpos($k, '_')) {
            // convert it to camel case
            $tmp = str_replace('_', ' ', strtolower($k));
            $tmp = trim(str_replace(' ', '', ucwords($tmp)));
            $tmp = strtolower(substr($tmp, 0, 1)).substr($tmp, 1);
            $result[$tmp] = $v;
        }
        return $result;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);
        // maybe we could do better but at the time it works, as all our entities are located in this namespace
        if(false === strpos(get_class($object), 'App\\Entity')) {
            return $data;
        }
        
        // TEST $data['jpPp'] = 'salut';
        // TEST $data['snake_case'] = 'ouaip';
        $d = $this->clonePropertiesToSnakeCase('data', $data);
        $result = $d['data'];
        
        return $result;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        if($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}