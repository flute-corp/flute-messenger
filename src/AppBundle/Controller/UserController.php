<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends AbstractController
{

    /**
     * @param $username
     * @return User|null|object
     *
     * @Rest\View(serializerGroups={"getUser"})
     */
    public function getUserAction($username)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('App:User')
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

        $em->flush();

        return $user;
    }

}
