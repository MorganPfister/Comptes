<?php

namespace CDC\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BudgetInstanceRepository extends EntityRepository {
    public function findBudgetInstanceUsingBudgetModele($budget_modele, $month, $year){
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.budgetmodele = :budget_modele')
                ->setParameter('budget_modele', $budget_modele)
            ->andWhere('MONTH(b.datestart) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(b.datestart) = :year')
                ->setParameter('year', $year);

        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function findBudgetInstanceUsingUserAndDate($user, $month, $year){
        $qb = $this->createQueryBuilder('b');
        $qb->innerJoin('b.budgetmodele', 'bm')
            ->innerJoin('bm.categorie', 'c', 'WITH', 'c.user = :user')
                ->setParameter('user', $user)
            ->where('bm.actif = :actif')
                ->setParameter('actif', true)
            ->andWhere('MONTH(b.datestart) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(b.datestart) = :year')
                ->setParameter('year', $year);

        $result = $qb->getQuery()->getResult();

        // Récupére le budget global
        $qb = $this->createQueryBuilder('b');
        $qb->innerJoin('b.budgetmodele', 'bm')
            ->where('bm.user = :user')
                ->setParameter('user', $user)
            ->andWhere('bm.actif = :actif')
                ->setParameter('actif', true)
            ->andWhere('MONTH(b.datestart) = :month')
                ->setParameter('month', $month)
            ->andWhere('YEAR(b.datestart) = :year')
                ->setParameter('year', $year);

        $budget_global = $qb->getQuery()->getResult();
        if ($budget_global){
            array_unshift($result, $budget_global[0]);
        }

        return $result;
    }
}