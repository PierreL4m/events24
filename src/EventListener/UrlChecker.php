<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class UrlChecker
{
    public function prePersist(LifecycleEventArgs $args)
    {        
        $this->checkMethod($args->getObject());
    }
     public function preUpdate(LifecycleEventArgs $args)
    {        
        $this->checkMethod($args->getObject());
    }

    public function checkMethod($entity)
    {
        if (method_exists($entity, "getUrl")) {            
            $this->checkUrl('Url', $entity);
        }
        if (method_exists($entity, "getFacebook")) { 
            $this->checkUrl('Facebook', $entity);
        }
        if (method_exists($entity, "getTwitter")) {            
            $this->checkUrl('Twitter', $entity);
        }
        if (method_exists($entity, "getViadeo")) {            
            $this->checkUrl('Viadeo', $entity);
        }
        if (method_exists($entity, "getLinkedin")) {            
            $this->checkUrl('Linkedin', $entity);
        }
    }
    public function checkUrl($field_name, $entity)
    {
        $function= 'get'.$field_name;
        $url = $entity->$function();
        
        if ($url){
            $last_char = substr($url, -1);
            
            if ($last_char == '/'){
                $url = substr($url, 0, (strlen($url) - 2 ));
            }
          
            if (preg_match( '/https?:/', $url) == false){
                if (method_exists($entity, "set".$field_name)){
                    $function= 'set'.$field_name;
                    $entity->$function('http://'.$url);
                }
                else{
                    new \Exception('method get'.$field_name.' found but no set'.$field_name);
                }
            }
        }
    }
}