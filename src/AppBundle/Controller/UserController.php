<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends AbstractController
{

    /**
     * @Rest\View(serializerGroups={"getConversation"})
     */
    public function getUserConversationsAction() {
        /**
         * @var $oUser User
         */
        $oUser = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:Conversation')
            ->getConversationsByUser($oUser);
    }

    /**
     * @param $term
     * @return User|null|object
     *
     * @Rest\View(serializerGroups={"getUser"})
     * @Rest\Get("user/find/{term}")
     */
    public function getUserFindAction($term)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:User')
            ->createQueryBuilder('user')
            ->select()
            ->where('user.username LIKE :term')
            ->setParameter('term', "%$term%")
            ->getQuery()->getResult();
    }

    /**
     * @param $username
     * @return User|null|object
     *
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getUserAction($username)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);

        if ($user) {
            return $user;
        }

        $newUser = new User($username);

        $em->persist($newUser);
        $em->flush();

        return $newUser;
    }

    /**
     * @param User $user
     * @return User|null|object
     *
     * @ParamConverter(
     *      "user",
     *      converter="fos_rest.request_body",
     *      options={
     *          "deserializationContext"={
     *              "groups"={"postUser"}
     *          }
     *      }
     *     )
     *
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function postUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('security.token_storage')->getToken()->getUser()->getId() !== $user->getId()) {
            throw new UnauthorizedHttpException('Même utilisateur', 'Hey ! Vous vous croyez où là ?');
        }

        try {
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new ConflictHttpException('Ce pseudo est déjà utilisé');
        }

        return $user;
    }

}
