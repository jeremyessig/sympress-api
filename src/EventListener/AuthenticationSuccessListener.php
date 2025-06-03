<?php


namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEventListener(event: "lexik_jwt_authentication.on_authentication_success", method: 'onAuthenticationSuccessResponse')]
class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['user'] = array(
            'roles' => $user->getRoles(),
            'username' => $user->getName(),
            'id' => $user->getId(),

        );

        $event->setData($data);
    }
}
