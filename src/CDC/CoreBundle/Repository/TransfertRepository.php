<?php

namespace CDC\CoreBundle\Repository;
use CDC\CoreBundle\Entity\Compte;
use Doctrine\ORM\EntityRepository;

class TransfertRepository extends EntityRepository {
    public function findTransfertUsingUser($user){
        $qb = $this->createQueryBuilder('t');
        $qb->innerJoin('t.compte', 'c', 'WITH', 'c.user = :user')
            ->setParameter('user', $user);

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function findTransfertUsingUserAndCategorie($user, $categorie){
        $qb = $this->createQueryBuilder('t');
        $qb->innerJoin('t.compte', 'c', 'WITH', 'c.user = :user')
            ->setParameter('user', $user)
            ->where('t.categorie = :categorie')
            ->setParameter('categorie', $categorie);

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getSumUsingCategorieAndDate($categorie, $month, $year){
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['SUM(t.montant)'])
            ->from('CDCCoreBundle:Transfert', 't')
            ->where('t.montant < 0')
            ->andWhere('MONTH(t.date) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(t.date) = :year')
                ->setParameter('year', $year)
            ->andWhere('t.categorie = :categorie')
                ->setParameter('categorie', $categorie);

        $result = $qb->getQuery()->getResult();

        if ($result) {
            $ret = $result[0][1];
        }
        else {
            $ret = null;
        }
        return $ret;
    }

    public function getSumDepenseByCategorie($user, $month = null, $year = null, Compte $compte=null){
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['ca.id, SUM(t.montant)'])
            ->from('CDCCoreBundle:Transfert', 't')
            ->innerJoin('t.categorie', 'ca', 'WITH', 'ca.user = :user')
                ->setParameter('user', $user);

        if($compte){
            $qb->innerJoin('t.compte', 'c', 'WITH', 'c.id = :id')
                ->setParameter('id', $compte->getId());
        }

        $qb->where('t.montant < 0');

        if ($month && $year){
            $qb->andWhere('MONTH(t.date) = :month')
                    ->setParameter('month', $month)
                ->andWhere('YEAR(t.date) = :year')
                    ->setParameter('year', $year);
        }

        $qb->groupBy('t.categorie');

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getGlobalSumUsingUserAndDate($user, $month, $year){
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['SUM(t.montant)'])
            ->from('CDCCoreBundle:Transfert', 't')
            ->innerJoin('t.compte', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->where('t.montant < 0')
            ->andWhere('MONTH(t.date) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(t.date) = :year')
                ->setParameter('year', $year);

        $result = $qb->getQuery()->getResult();

        if ($result) {
            $ret = $result[0][1];
        }
        else {
            $ret = null;
        }
        return $ret;
    }

    public function getDepenseUsingDateAndCompte($user, $month, $year, Compte $compte=null){
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['SUM(t.montant)'])
            ->from('CDCCoreBundle:Transfert', 't')
            ->innerJoin('t.compte', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->where('t.montant < 0')
            ->andWhere('MONTH(t.date) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(t.date) = :year')
                ->setParameter('year', $year);

        if ($compte){
            $qb->andWhere('c.id = :id')
                ->setParameter('id', $compte->getId());
        }

        $result = $qb->getQuery()->getResult();

        if ($result) {
            $ret = $result[0][1];
        }
        else {
            $ret = null;
        }
        return $ret;
    }

    public function getIncomeUsingDateAndCompte($user, $month, $year, Compte $compte=null){
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['SUM(t.montant)'])
            ->from('CDCCoreBundle:Transfert', 't')
            ->innerJoin('t.compte', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->where('t.montant > 0')
            ->andWhere('MONTH(t.date) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(t.date) = :year')
                ->setParameter('year', $year);

        if ($compte){
            $qb->andWhere('c.id = :id')
                ->setParameter('id', $compte->getId());
        }

        $result = $qb->getQuery()->getResult();

        if ($result) {
            $ret = $result[0][1];
        }
        else {
            $ret = null;
        }
        return $ret;
    }
}