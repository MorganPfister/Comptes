<?php

namespace CDC\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BudgetInstance")
 * @ORM\Entity(repositoryClass="CDC\CoreBundle\Repository\BudgetInstanceRepository")
 */
class BudgetInstance {
    protected $currentvalue;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="BudgetModele")
     * @ORM\JoinColumn(name="budgetmodele_id", referencedColumnName="id")
     **/
    protected $budgetmodele;

    /**
     * @var @ORM\Column(type="decimal")
     */
    protected $seuil;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datestart;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateend;

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
     * Set datestart
     *
     * @param \DateTime $datestart
     *
     * @return BudgetInstance
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
     * Set dateend
     *
     * @param \DateTime $dateend
     *
     * @return BudgetInstance
     */
    public function setDateend($dateend)
    {
        $this->dateend = $dateend;

        return $this;
    }

    /**
     * Get dateend
     *
     * @return \DateTime
     */
    public function getDateend()
    {
        return $this->dateend;
    }

    /**
     * Set budgetmodele
     *
     * @param \CDC\CoreBundle\Entity\BudgetModele $budgetmodele
     *
     * @return BudgetInstance
     */
    public function setBudgetmodele(\CDC\CoreBundle\Entity\BudgetModele $budgetmodele = null)
    {
        $this->budgetmodele = $budgetmodele;

        return $this;
    }

    /**
     * Get budgetmodele
     *
     * @return \CDC\CoreBundle\Entity\BudgetModele
     */
    public function getBudgetmodele()
    {
        return $this->budgetmodele;
    }

    /**
     * Set seuil
     *
     * @param $seuil
     *
     * @return BudgetInstance
     */
    public function setSeuil($seuil)
    {
        $this->seuil= $seuil;

        return $this;
    }

    /**
     * Get seuil
     *
     * @return \CDC\CoreBundle\Entity\BudgetModele
     */
    public function getSeuil()
    {
        return $this->seuil;
    }

    /**
     * Set current value
     *
     * @param $value
     *
     * @return BudgetInstance
     */
    public function setCurrentvalue($value){
        $this->currentvalue = $value;

        return $this;
    }

    /**
     * Get current value
     *
     * @return float
     */
    public function getCurrentvalue(){
        return $this->currentvalue;
    }
}
