<?php

namespace CDC\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategorieRepository extends EntityRepository {
    public function getParentCategorie_a($user){
        $qb = $this->createQueryBuilder('u');
        $qb->where('u.parent IS NULL')
            ->andWhere('u.user = :user')
                ->setParameter('user', $user)
            ->andWhere('u.actif = :actif')
                ->setParameter('actif', true);

        return $qb->getQuery()
            ->getResult();
    }

}