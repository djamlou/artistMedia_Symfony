<?php
namespace App\Controller;

use App\Entity\Album;
use App\Form\FormAlbumType;
use App\Form\SearchFormAlbumType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AlbumController extends Controller{

    /**
     *@Route("/album/add", name="add_album_route")
     */

    public function addAlbum(Request $request){
            $album = new Album();
            $form =$this->createForm(FormAlbumType::class, $album);
            $form->handleRequest($request);

            $msg = "";

            if ($form->isSubmitted()) {

                if ($form->isValid()) {

                    $file = $album->getImg();
                    $fileName = $form['title']->getData().'.'.$file->guessExtension();
                    // moves the file to the directory where img are stored
                    $file->move(

                        $this->getParameter('img_directory2'),

                        $fileName
                    );

                    $album->setImg('assets/img/album/'.$fileName);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($form->getData());
                    $manager->flush();

                    $msg = "Album ajouté en base avec succès";

                } else {
                    $msg = "Vous avez oublié de remplire un champs";
                }

            }
            return $this->render('album/addAlbum.html.twig', array("albumForm"=>$form->createView(),
                'msg'=>$msg));
    }

    /**
     *@Route("/album/{id}", name="album_show_route")
     */
    public function showAlbum(Album $album){

        return $this->render('album/album.html.twig', array('album'=>$album));
    }

    /**
     * @Route("/api/album", name="rest_album")
     * @Method({"GET"})
     */
    public function getAlbumsREST(Request $request){

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $em = $this->getDoctrine()->getRepository(Album::class);
        $albums = $em->findAll();

        foreach ($albums as &$current){
            $current->setImg($request->getUriForPath('/'.$current->getImg()));
        }
        $normalizer->setCircularReferenceHandler(
            function ($object){
                return $object->getId();
            }
        );

        $options = array( "attributes"=>array('id', 'title', 'img', 'releaseDate', 'artistId'));
        $rep = new Response(
            $serializer->serialize($albums, 'json', $options)
        );
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        return $rep;

    }

    /**
     * @Route("/album/{id}/edit", name="album_edit_route")
     */
    public function editAlbum(Album $album, Request $request)
    {
        $form = $this->createForm(FormAlbumType::class, $album);

        $form->handleRequest($request);
        $msg = "";

        if ($form->isSubmitted()) {
          if($form->isValid()) {

              $doctrine = $this->getDoctrine();
              $em = $doctrine->getManager();
              $em->persist($album);
              $em->flush();
              $msg = "Album modifié avec succès";
              return $this->redirectToRoute('artist_show_route', ['id' => $album->getArtist()->getId()]);
          }else{
              $msg = "Vous avez oublié de remplire un champs";
          }

        }

        return $this->render('album/addAlbum.html.twig', [
            'album' => $album,
            'albumForm' => $form->createView(), 'msg' =>$msg]);
    }

    /**
     *@Route("/album/{id}/delete", name="album_delete_route")
     */

    public function deleteAlbum(Album $album){
        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->flush();

        return $this->render('album/deleteAlbum.html.twig', array('album' => $album));
    }

    /**
     * @Route("/albums/search", name="search_album_route")
     */
    public function searchAlbum(Request $request)
    {
        $form = $this->createForm(SearchFormAlbumType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine()->getRepository(Album::class);
            $album = $doctrine->findOneBytitle($form['title']->getData());
            return $this->redirectToRoute('album_show_route', ['id' => $album->getId()]);

        }
        return $this->render('album/search.html.twig', [
            'searchForm' => $form->createView()]);
    }


}