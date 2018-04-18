<?php
/**
 * Created by PhpStorm.
 * User: tomcio
 * Date: 18.04.18
 * Time: 11:40
 */

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\User;

class UserOperations {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function isLogin(){
        $session = new Session();

        if($session->has('logged') && $session->get('logged'))
            return true;

        return false;
    }

    public function checkUser($login, $password){
        $user = $this->em->getRepository(User::class)->findOneBy([
            'name' => $login
        ]);

        if($user && password_verify($password, $user->getPassword()))
            return true;

        return false;
    }

}