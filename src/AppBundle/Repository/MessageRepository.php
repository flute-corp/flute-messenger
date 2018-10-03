<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getMessagesByConversation($conversation, Message $message = null)
    {
        $qb = $this->createQueryBuilder('message')
            ->select()
            ->where('message.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->orderBy('message.dateEtHeure')
            ->setMaxResults(10);

        if ($message) {
            $qb->andWhere('message.id < :message')
                ->setParameter('message', $message);
        }

        return $qb->getQuery()->getResult();
    }
}
