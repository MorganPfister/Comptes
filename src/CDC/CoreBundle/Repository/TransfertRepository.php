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
}