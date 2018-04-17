<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login",
     *      methods={"POST", "GET"}
     * )
     */
    public function login(){
        $session = new Session();
        $session->start();
        $session->set('logged', true);

        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
        $session = new Session();
        $session->set('logged', false);
        $session->invalidate();

        return $this->redirectToRoute('tree');
    }
}
