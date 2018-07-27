<?php

namespace CDC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Transfert")
 * @ORM\Entity(repositoryClass="CDC\CoreBundle\Repository\TransfertRepository")
 */
class Transfert {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $montant;

    /**
     * @ORM\Column(type="string")
     */
    protected $titre;

    /**
     * @ORM\Column(type="text")
     */
    protected $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\Compte")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $compte;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\Categorie")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\MoyenTransfert")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $moyen;

    /**
     * @ORM\ManyToOne(targetEntity="CDC\CoreBundle\Entity\TypeTransfert")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateadd;

    public function __construct() {
        $this->dateadd = new \DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Transfert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Transfert
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Transfert
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Transfert
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set dateadd
     *
     * @param \DateTime $dateadd
     *
     * @return Transfert
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
     * Set compte
     *
     * @param \CDC\CoreBundle\Entity\Compte $compte
     *
     * @return Transfert
     */
    public function setCompte(\CDC\CoreBundle\Entity\Compte $compte)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return \CDC\CoreBundle\Entity\Compte
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set lastCompte
     *
     * @param \CDC\CoreBundle\Entity\Compte $lastCompte
     *
     * @return Transfert
     */
    public function setLastCompte(\CDC\CoreBundle\Entity\Compte $lastCompte = null)
    {
        $this->last_compte = $lastCompte;

        return $this;
    }

    /**
     * Get lastCompte
     *
     * @return \CDC\CoreBundle\Entity\Compte
     */
    public function getLastCompte()
    {
        return $this->last_compte;
    }

    /**
     * Set categorie
     *
     * @param \CDC\CoreBundle\Entity\Categorie $categorie
     *
     * @return Transfert
     */
    public function setCategorie(\CDC\CoreBundle\Entity\Categorie $categorie)
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
     * Set moyen
     *
     * @param \CDC\CoreBundle\Entity\MoyenTransfert $moyen
     *
     * @return Transfert
     */
    public function setMoyen(\CDC\CoreBundle\Entity\MoyenTransfert $moyen = null)
    {
        $this->moyen = $moyen;

        return $this;
    }

    /**
     * Get moyen
     *
     * @return \CDC\CoreBundle\Entity\MoyenTransfert
     */
    public function getMoyen()
    {
        return $this->moyen;
    }

    /**
     * Set type
     *
     * @param \CDC\CoreBundle\Entity\TypeTransfert $type
     *
     * @return Transfert
     */
    public function setType(\CDC\CoreBundle\Entity\TypeTransfert $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \CDC\CoreBundle\Entity\TypeTransfert
     */
    public function getType()
    {
        return $this->type;
    }
}
