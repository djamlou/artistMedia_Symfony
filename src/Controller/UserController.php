<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\FormUserType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/register", name="register_route")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $form = $this->createForm(FormUserType::class, $user);
        $form->handleRequest($request);

        $msg = "";
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();

                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);

                $manager->persist($form->getData());
                $manager->flush();

                $msg = "Utilisateur ajouté en base avec succès";

            } else {
                $msg = "Vous avez oublié de remplire un champs";
            }

        }
        return $this->render('newUser.html.twig', array("userForm"=>$form->createView(),
            'msg'=>$msg));
    }
}