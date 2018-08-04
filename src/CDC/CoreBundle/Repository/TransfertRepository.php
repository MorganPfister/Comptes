<?php

namespace CDC\CoreBundle\Repository;
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

    public function getSumGroupByCategorieUsingUserAndDate($user, $month, $year){
        $query = $this->_em->createQuery('
            SELECT IDENTITY(t.categorie), SUM(t.montant)
            FROM CDCCoreBundle:Transfert t
            INNER JOIN CDCCoreBundle:Compte c 
              WITH t.compte = c
            INNER JOIN CDCCoreBundle:Categorie ca
              WITH t.categorie = ca
            WHERE ca.actif = true
              AND MONTH(t.date) = ?1
              AND YEAR(t.date) = ?2
              AND c.user = ?3
              AND t.montant < 0
            GROUP BY t.categorie
        ');
        $query->setParameter(1, $month)
            ->setParameter(2, $year)
            ->setParameter(3, $user);

        $result = $query->getResult();
        return $result;
    }

    public function getSumUsingCategorieAndDate($categorie, $month, $year){
        $query = $this->_em->createQuery('
            SELECT SUM(t.montant)
            FROM CDCCoreBundle:Transfert t
            WHERE t.categorie = ?1
              AND t.montant < 0
              AND MONTH(t.date) = ?2
              AND YEAR(t.date) = ?3
            GROUP BY t.categorie
        ');
        $query->setParameter(1, $categorie)
            ->setParameter(2, $month)
            ->setParameter(3, $year);

        $result = $query->getResult();
        if ($result) {
            $ret = $result[0][1];
        }
        else {
            $ret = null;
        }
        return $ret;
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
}