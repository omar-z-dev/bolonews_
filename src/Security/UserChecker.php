<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;




/*une classe que le composant Security appelle automatiquement pendant la connexion.*/
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        //si l’objet reçu n’est pas une instance de l'entité App\Entity\User, on arrête cette vérification.
        if (!$user instanceof User) {
            return;
        }

        //si l’utilisateur est banni on arrête la connexion et on affiche un message
        if ($user->isBanned()) {
            throw new CustomUserMessageAccountStatusException(
                'Vous êtes banni. Vous ne pouvez pas vous connecter, veuillez contacter un administrateur.'
            );
        }
    }

    //vérification n’est utile que si  mis en place la confirmation du compte par email. 
    public function checkPostAuth(UserInterface $user): void
    {
    }
}