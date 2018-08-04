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

    public function findBudgetModeleUsingUser($user){
        // Récupère les budget par catégorie
        $qb = $this->createQueryBuilder('b');
        $qb->innerJoin('b.categorie', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->Where('b.actif = :actif')
                ->setParameter('actif', true);

        $result = $qb->getQuery()->getResult();

        // Récupére le budget global
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.user = :user')
                ->setParameter('user', $user)
            ->andWhere('b.actif = :actif')
                ->setParameter('actif', true);

        $budget_global = $qb->getQuery()->getResult();
        array_push($result, $budget_global[0]);

        return $result;
    }
}