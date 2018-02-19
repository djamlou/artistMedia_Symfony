<?php
namespace App\Controller;

use App\Entity\Album;
use App\Form\FormAlbumType;
use App\Form\SearchFormAlbumType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/album/{id}/edit", name="album_edit_route")
     */
    public function editAction(Album $album, Request $request)
    {
        $form = $this->createForm(FormAlbumType::class, $album);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();
            $em->persist($album);
            $em->flush();

            return $this->redirectToRoute('album_edit_route', ['id' => $album->getArtist()->getId()]);

        }

        return $this->render('album/edit.html.twig', [
            'album' => $album,
            'albumForm' => $form->createView()]);
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
    public function searchArtist(Request $request)
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