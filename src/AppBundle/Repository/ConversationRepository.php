<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ConversationRepository extends EntityRepository
{
    public function getConversationsByUser(User $oUser) {
        $qb = $this->createQueryBuilder('conv')
            ->select()
            ->leftJoin('conv.createdBy', 'creator')
            ->addSelect('creator')
            ->leftJoin('conv.participants', 'user')
            ->addSelect('user')
            ->where('creator = :user')
            ->orWhere('user IN (:user)')
            ->setParameters([
                'user'=> $oUser
            ])
        ;

        return $qb->getQuery()->getResult();
    }
}
