<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\FormUserType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

    /**
     * @Route("/api/register", name="api_register")
     * @Method({"OPTIONS", "POST"})
     */

    public function apiRegister(Request $request, UserPasswordEncoderInterface $encoder ){

        if($request->getMethod() == "POST"){
            $user = new User();
            $form = json_decode($request->getContent());

            $manager = $this->getDoctrine()->getManager();

            $encoded = $encoder->encodePassword($user, $form->password);
            $user->setPassword($encoded);
            $user->setUsername($form->username);
            $user->setEmail($form->email);

            $manager->persist($user);
            $manager->flush();

        }

        // c'est ici qu'on recupere les donnees direction -> bdd
        $rep = new Response(null);
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $rep->headers->set('Access-Control-Allow-Methods', "GET,POST,PUT,DELETE,OPTIONS");
        $rep->headers->set('Access-Control-Allow-Headers', "Content-Type");
        return $rep;

    }

    /**
     * @Route("/api/auth", name="api_auth")
     * @Method({"OPTIONS", "POST"})
     */

    public function apiAuth(Request $request ){
        $user           = $this->getUser();
        $encoder        = new JsonEncoder();
        $normalizer     = new ObjectNormalizer();
        $serializer     = new Serializer(array($normalizer), array($encoder));
        // empêche les références circulaires
        $normalizer->setCircularReferenceHandler(
            function ($object) {
                return $object->getId();
            }
        );
        // est là pour définir les attributs exportés en json
        $options = array(
            "attributes"=>array("id", "username", "email", "role", "isActive")
        );
        $rep = new Response($serializer->serialize($user, 'json', $options));
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $rep->headers->set('Access-Control-Allow-Methods', "GET,POST,PUT,DELETE,OPTIONS");
        $rep->headers->set('Access-Control-Allow-Headers', "Content-Type");
        return $rep;

    }
    /**
     * @Route("/api/logout", name="api_logout_preflight")
     * @Method({"OPTIONS", "GET"})
     */
    public function apiLogout(Request $request){
        $user           = $this->getUser();
        $encoder        = new JsonEncoder();
        $normalizer     = new ObjectNormalizer();
        $serializer     = new Serializer(array($normalizer), array($encoder));
        // empêche les références circulaires
        $normalizer->setCircularReferenceHandler(
            function ($object) {
                return $object->getId();
            }
        );
        // est là pour définir les attributs exportés en json
        $options = array(
            "attributes"=>array("id", "username", "email", "role", "isActive")
        );
        $rep = new Response($serializer->serialize($user, 'json', $options));
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $rep->headers->set('Access-Control-Allow-Methods', "GET,POST,PUT,DELETE,OPTIONS");
        $rep->headers->set('Access-Control-Allow-Headers', "Content-Type");
        return $rep;
    }

}