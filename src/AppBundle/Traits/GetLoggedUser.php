<?php

namespace AppBundle\Traits;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Trait GetLoggedUser
 *
 * @package AppBundle\Traits
 */
trait GetLoggedUser {

    /**
     * @return User
     */
    private function _getLoggedUser() {
        /**
         * @var $srvTokenStorage TokenStorage
         */
        $srvTokenStorage = $this->get('security.token_storage');
        return $srvTokenStorage->getToken()->getUser();
    }
}
