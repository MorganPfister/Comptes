<?php

namespace CDC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="Categorie")
 * @ORM\Entity(repositoryClass="CDC\CoreBundle\Repository\CategorieRepository")
 */
class Categorie {
    const UNKNOWN_CATEGORIE_ID = 14;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $titre;

    /**
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Categorie", mappedBy="parent")
     **/
    protected $children;

    /**
     * @ORM\Column(type="string")
     */
    protected $icon;

    /**
     * @ORM\Column(type="string")
     */
    protected $color;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datestart;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $actif;

    /** @var BudgetModele */
    protected $budgetmodele;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->datestart = new \DateTime();
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Categorie
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Categorie
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set datestart
     *
     * @param \DateTime $datestart
     *
     * @return Categorie
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
     * Get budgetmodele
     *
     * @return BudgetModele
     */
    public function getBudgetmodele()
    {
        return $this->budgetmodele;
    }

    /**
     * Set parent
     *
     * @param \CDC\CoreBundle\Entity\Categorie $parent
     *
     * @return Categorie
     */
    public function setParent(\CDC\CoreBundle\Entity\Categorie $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CDC\CoreBundle\Entity\Categorie
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \CDC\CoreBundle\Entity\Categorie $child
     *
     * @return Categorie
     */
    public function addChild(\CDC\CoreBundle\Entity\Categorie $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CDC\CoreBundle\Entity\Categorie $child
     */
    public function removeChild(\CDC\CoreBundle\Entity\Categorie $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set user
     *
     * @param \CDC\CoreBundle\Entity\User $user
     *
     * @return Categorie
     */
    public function setUser(\CDC\CoreBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set budgetmodele
     *
     * @param BudgetModele $budgetmodele
     *
     * @return $this
     */
    public function setBudgetmodele(\CDC\CoreBundle\Entity\BudgetModele $budgetmodele){
        $this->budgetmodele = $budgetmodele;

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
     * Set color
     *
     * @param string $color
     *
     * @return Categorie
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Categorie
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
}
