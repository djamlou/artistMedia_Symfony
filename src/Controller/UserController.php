<?php
namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(Request $request, AuthenticationUtils $utils ){
        // get the login error if there is one
        $error = $utils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $utils->getLastUsername();
        return $this->render(
            'user.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }
}