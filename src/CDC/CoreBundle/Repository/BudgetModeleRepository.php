<?php

namespace CDC\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BudgetModeleRepository extends EntityRepository {
    public function findBudgetModeleUsingUserAndCategorie($user, $categorie){
        $qb = $this->createQueryBuilder('b');
        $qb->innerJoin('b.categorie', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->where('b.categorie = :categorie')
                ->setParameter('categorie', $categorie)
            ->andWhere('b.actif = :actif')
                ->setParameter('actif', true);

        $result = $qb->getQuery()->getResult();
        return $result;
    }
}