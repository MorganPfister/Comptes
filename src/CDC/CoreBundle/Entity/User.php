<?php

namespace CDC\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Utilisateur")
 */
class User extends BaseUser {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function getLastcompte(){
        return $this->lastcompte;
    }

    public function setLastcompte($compte){
        $this->lastcompte = $compte;
        return $this;
    }

    public function __construct() {
        parent::__construct();
    }
}