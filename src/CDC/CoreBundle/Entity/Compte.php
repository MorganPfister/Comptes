<?php

namespace CDC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Compte")
 * @ORM\Entity(repositoryClass="CDC\CoreBundle\Repository\CompteRepository")
 */
class Compte {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $titulaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $banque;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $solde;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datestart;

    public function __construct() {
        $this->datestart = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulaire
     *
     * @param string $titulaire
     *
     * @return Compte
     */
    public function setTitulaire($titulaire)
    {
        $this->titulaire = $titulaire;

        return $this;
    }

    /**
     * Get titulaire
     *
     * @return string
     */
    public function getTitulaire()
    {
        return $this->titulaire;
    }

    /**
     * Set banque
     *
     * @param string $banque
     *
     * @return Compte
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return string
     */
    public function getBanque()
    {
        return $this->banque;
    }

    /**
     * Set datestart
     *
     * @param \DateTime $datestart
     *
     * @return Compte
     */
    public function setDatestart($datestart)
    {
        $this->datestart = $datestart;

        return $this;
    }

    /**
     * Get datestart
     *
     * @return \DateTime
     */
    public function getDatestart()
    {
        return $this->datestart;
    }

    /**
     * Set user
     *
     * @param \CDC\CoreBundle\Entity\User $user
     *
     * @return Compte
     */
    public function setUser(\CDC\CoreBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CDC\CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set solde
     *
     * @param string $solde
     *
     * @return Compte
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return string
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Compte
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }
}
