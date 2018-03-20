<?php
namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Genre;
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

               $file = $artist->getImg();
               $fileName = $form['name']->getData().'.'.$file->guessExtension();
                   $file->move(

                   $this->getParameter('img_directory'),

                   $fileName
               );

               $artist->setImg('assets/img/artist/'.$fileName);

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
     * @Route("/api/artist/add", name="rest_add")
     * @Method({"OPTIONS", "POST"})
     */
    public function apiAdd(Request $request){

        if( $request->getMethod() === "POST"){

            $raw = $request->getContent();
            $data = json_decode($raw);

            $fileName =$data->name.'.jpg';

            $artist = new Artist();

            $artist->setName($data->name);
            $artist->setImg($this->getParameter('img_directory').$fileName);

            $artist->setGenres(array());
            $artist->setAlbums(array());

            if( $data->imgData){
                $imgdata = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data->imgData));
                file_put_contents( $artist->getImg(), $imgdata );
            }





            $mgr = $this->getDoctrine()->getManager();
            $mgr->persist($artist);
            $mgr->flush();

            $rep = new Response('{"msg":"OK"}');

        }
        else{
            $rep = new Response('{"msg":"OK"}');
        }
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $rep->headers->set('Access-Control-Allow-Methods', "GET,POST,PUT,DELETE,OPTIONS");
        $rep->headers->set('Access-Control-Allow-Headers', "Content-Type");
        return $rep;
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
    public function getArtistsREST(Request $request){

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

        $options = array(
            "attributes"=>array(
            'id',
            'name',
            'img',
            'genres' => array(
                "id",
                "name"
            ),
                'albums' => array(
                        "id"
                    )
            )
        );
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
     * @Route("/api/artist/delete/{id}", name="rest_delete")
     * @Method({"OPTIONS", "DELETE"})
     */
    public function apiDelete(Request $request, Artist $artist){

        if( $request->getMethod() === "DELETE"){
            //$this->denyAccessUnlessGranted('', null, '{"msg":"Unable to access this page!"}');
            if( $artist !== null ){
                $id = $artist->getId();
                $man = $this->getDoctrine()->getManager();
                $man->remove($artist);
                $man->flush();
                $rep = new Response('{"msg":"OK", "id": '.$id.'}');
            }
            else{
                $rep = new Response('{"msg":"no artist for that id"}');
            }

        }
        else{
            $rep = new Response('{"msg":"OK"}');
        }
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        $rep->headers->set('Access-Control-Allow-Methods', "GET,POST,PUT,DELETE,OPTIONS");
        $rep->headers->set('Access-Control-Allow-Headers', "Content-Type");
        return $rep;

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

                $file = $artist->getImg();
                $fileName = $form['name']->getData().'.'.$file->guessExtension();
                $file->move(

                    $this->getParameter('img_directory'),

                    $fileName
                );

                $artist->setImg('assets/img/artist/'.$fileName);

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

    /**
     * @Route("/artists/search", name="search_artist_route")
     */
    public function searchArtist(Request $request)
    {

        $form = $this->createForm(SearchFormArtistType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine()->getRepository(Artist::class);
            $artist = $doctrine->findOneByName($form['name']->getData());
            return $this->redirectToRoute('artist_show_route', ['id' => $artist->getId()]);
        }
        return $this->render('artist/searchArtist.html.twig', [
            'searchForm' => $form->createView()]);
    }

    /**
     * @Route("/api/genres", name="rest_genres")
     * @Method({"GET"})
     */
    public function apiGenres(Request $request){

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $em = $this->getDoctrine()->getRepository(Genre::class);
        $genres = $em->findBy(
            array(),
            ['name' => 'ASC']
        );

        $normalizer->setCircularReferenceHandler(
            function ($object){
                return $object->getId();
            }
        );

        $options = array(
            "attributes"=>array(
                'name',
            )
        );
        $rep = new Response(
            $serializer->serialize($genres, 'json', $options)
        );
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        return $rep;

    }

}