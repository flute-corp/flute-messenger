<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getMessagesByConversation($conversation, \DateTime $date = null)
    {
        $qb = $this->createQueryBuilder('message')
            ->select()
            ->where('message.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->orderBy('message.dateEtHeure', 'desc')
            ->setMaxResults(10);

        if ($date) {
            $qb->andWhere('message.dateEtHeure <= :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getResult();
    }
}
