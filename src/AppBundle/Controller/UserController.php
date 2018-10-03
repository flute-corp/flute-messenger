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
