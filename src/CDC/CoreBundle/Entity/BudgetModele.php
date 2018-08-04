<?php

namespace CDC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BudgetModele")
 * @ORM\Entity(repositoryClass="CDC\CoreBundle\Repository\BudgetModeleRepository")
 */
class BudgetModele {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal")
     */
    private $seuil;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateadd;

    /**
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumn(nullable=true, name="categorie_id", referencedColumnName="id")
     **/
    private $categorie;

    /**
     * @ORM\OneToOne(targetEntity="CDC\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true, name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    public function __construct() {
        $this->dateadd = new \DateTime();
        $this->actif = true;
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
     * Set seuil
     *
     * @param string $seuil
     *
     * @return BudgetModele
     */
    public function setSeuil($seuil)
    {
        $this->seuil = $seuil;

        return $this;
    }

    /**
     * Get seuil
     *
     * @return string
     */
    public function getSeuil()
    {
        return $this->seuil;
    }

    /**
     * Set dateadd
     *
     * @param \DateTime $dateadd
     *
     * @return BudgetModele
     */
    public function setDateadd($dateadd)
    {
        $this->dateadd = $dateadd;

        return $this;
    }

    /**
     * Get dateadd
     *
     * @return \DateTime
     */
    public function getDateadd()
    {
        return $this->dateadd;
    }

    /**
     * Set categorie
     *
     * @param \CDC\CoreBundle\Entity\Categorie $categorie
     *
     * @return BudgetModele
     */
    public function setCategorie(\CDC\CoreBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \CDC\CoreBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return BudgetModele
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return BudgetModele
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
