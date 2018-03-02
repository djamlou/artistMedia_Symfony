<?php
namespace App\Controller;

use App\Entity\Artist;
use App\Form\SearchFormArtistType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FormArtistType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ArtistController extends Controller{

    /**
     *@Route("/artist/add", name="add_artist_route")
     */
    public function addArtist(Request $request){
        $artist = new Artist();
       $form =$this->createForm(FormArtistType::class, $artist);
       $form->handleRequest($request);

       $msg = "";

       if ($form->isSubmitted()) {

           if ($form->isValid()) {
               $manager = $this->getDoctrine()->getManager();
               $manager->persist($form->getData());
               $manager->flush();

               $msg = "Artiste ajouté en base avec succès";
               header ('Location: {{ path("list_route") }}' );

           } else {
    $msg = "Vous avez oublié de remplire un champs";
}

}
return $this->render('artist/addArtist.html.twig', array("artistForm"=>$form->createView(),
    'msg'=>$msg));
}

    /**
     *@Route("/list", name="list_route")
     */
    public function getArtists(){

        $em = $this->getDoctrine()->getRepository(Artist::class);
        $artists = $em->findAll();
        return $this->render('artist/list.html.twig', array(
            'artists' => $artists
        ));

    }

    /**
     * @Route("/api/list", name="rest_list")
     * @Method({"GET"})
     */
    public function getArtistREST(Request $request){

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $em = $this->getDoctrine()->getRepository(Artist::class);
        $artists = $em->findAll();

        foreach ($artists as &$current){
            $current->setImg($request->getUriForPath('/'.$current->getImg()));
        }
        $normalizer->setCircularReferenceHandler(
            function ($object){
                return $object->getId();
            }
        );

        $options = array( "attributes"=>array('id', 'name', 'img'));
        $rep = new Response(
            $serializer->serialize($artists, 'json', $options)
        );
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        return $rep;



    }

    /**
     *@Route("/artist/{id}", name="artist_show_route")
     */
    public function showArtist(Artist $artist){

        return $this->render('artist/artist.html.twig', array('artist'=>$artist));
    }

    /**
     *@Route("/artist/{id}/delete", name="artist_delete_route")
     */

    public function deleteArtist(Artist $artist){
        $em = $this->getDoctrine()->getManager();
        $em->remove($artist);
        $em->flush();
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        return $this->render('artist/deleteArtist.html.twig', array('artist' => $artist));
    }

    /**
     *@Route("/artist/{id}/edit", name="artist_edit_route")
     */

    public function editArtist(Artist $artist, Request $request){
        $form = $this->createForm(FormArtistType::class, $artist);
        $form->handleRequest($request);
        $msg = "";

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($artist);
                $manager->flush();

                $msg = "Artist modifié avec succès";

            } else {
                $msg = "Vous avez oublié de remplire un champs";
            }
        }

        return $this->render('artist/addArtist.html.twig', array(
            'artistForm' => $form->createView(),'msg'=>$msg));

    }

    /*/**
     * @Route("/artists/search", name="search_artist_route")
     */
    /*public function searchArtist(Request $request)
    {

        $form = $this->createForm(SearchFormArtistType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine()->getRepository(Artist::class);
            $artist = $doctrine->findOneByName($_POST['name']->getData());
            return $this->redirectToRoute('artist_show_route', ['id' => $artist->getId()]);
        }
        return $this->render('artist/searchArtist.html.twig', [
            'searchForm' => $form->createView()]);
    }*/

}