<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conversation;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConversationController extends FOSRestController
{

    /**
     * @Rest\View(serializerGroups={"getMessage"})
     * @return array
     */
    public function getConversationAction(Conversation $conversation)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Message')->getMessagesByConversation($conversation);
    }

    /**
     * @Rest\View(serializerGroups={"getMessage"})
     * @return array
     */
    public function getConversationBeforeAction(Conversation $conversation, Message $message)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Message')->getMessagesByConversation($conversation, $message);
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
        $em->flush();
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
     * @param Conversation $conv
     * @param Message $message
     * @return Message
     *
     * @ParamConverter(
     *      "message",
     *      converter="fos_rest.request_body",
     *      options={
     *          "deserializationContext"={
     *              "groups"={"postMessage"}
     *          }
     *      }
     *     )
     * @Rest\View(serializerGroups={"getMessage"})
     */
    public function postConversationMessageAction(Conversation $conv, Message $message)
    {
        $oUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($message->getId()) {
            throw new BadRequestHttpException('Dit c\'est dit ! On revient pas sur sa parole !');
        }

        if (!$conv->getAllParticipants()->contains($oUser)) {
            throw new UnauthorizedHttpException(
                'Etre dans la conversation',
                'Vous devez faire parti de la conversation');
        }

        $message->setConversation($conv);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();
        return $message;
    }

}
