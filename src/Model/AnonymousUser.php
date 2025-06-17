<?php
namespace App\Model;

namespace League\Bundle\OAuth2ServerBundle\Security\User\NullUser;

class AnonymousUser extends NullUser {
    
    public function getType() {
        return 'anonymous';
    }
}