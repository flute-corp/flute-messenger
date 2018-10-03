<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conversation;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConversationController
{

    /**
     * @param Conversation $conversation
     * @return Conversation
     *
     * @ParamConverter(
     *      "conversation",
     *      converter="fos_rest.request_body",
     *      options={
     *          "deserializationContext"={
     *              "groups"={"postConversation"}
     *          }
     *      }
     *     )
     * @Rest\View(serializerGroups={"getConversation"})
     */
    public function postConversationAction(Conversation $conversation)
    {
        if ($conversation->getId()) throw new BadRequestHttpException('Vous devez put pour mettre à jour');

        $oUser = $this->get('security.token_storage')->getToken()->getUser();
        $aPart = $conversation->getParticipants();
        if ($aPart->contains($oUser)) {
            throw new ConflictHttpException('Vous ne pouvez pas vous désigner participant');
        }
        if (!$aPart) {
            throw new NotAcceptableHttpException('Vous devez définir au moins un participant');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($conversation);
//        $em->flush();
        return $conversation;
    }

    /**
     * @param Conversation $conversation
     * @return Conversation
     *
     * @ParamConverter(
     *      "conversation",
     *      converter="fos_rest.request_body",
     *      options={
     *          "deserializationContext"={
     *              "groups"={"putConversation"}
     *          }
     *      }
     *     )
     * @Rest\View(serializerGroups={"getConversation"})
     */
    public function putConversationAction(Conversation $conv, Conversation $conversation)
    {
        $oUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$conversation->getParticipants()->contains($oUser)) {
            throw new UnauthorizedHttpException(
                'Etre dans la conversation',
                'Vous devez faire parti de la conversation');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($conversation);
        $em->flush();
        return $conversation;
    }


    /**
     * @Rest\View()
     * @return array
     */
    public function getConversationAction()
    {
        return [];
    }
}
