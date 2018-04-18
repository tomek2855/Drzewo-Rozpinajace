<?php

namespace App\Controller;

use App\Utils\UserOperations;
use Symfony\Component\HttpFoundation\Request;
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
    public function login(Request $request, UserOperations $ue){
        $session = new Session();
        $session->start();
        $session->set('logged', false);

        $errors = false;

        if($request->request->has('login') && $request->request->has('password')){
            $login = $request->request->get('login');
            $password = $request->request->get('password');

            if($ue->checkUser($login, $password)){
                $session->set('logged', true);
                return $this->redirectToRoute('category-show-depth');
            }
            else {
                $errors = true;
            }
        }

        return $this->render('user/login.html.twig', [
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
        $session = new Session();
        $session->set('logged', false);
        $session->invalidate();

        return $this->redirectToRoute('category');
    }
}
