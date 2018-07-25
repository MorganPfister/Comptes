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
}