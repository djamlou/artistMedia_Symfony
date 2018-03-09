<?php
namespace App\Controller;

use App\Form\SearchFormArtistType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Artist;
use App\Entity\Album;
use App\Entity\Track;

class SearchController extends Controller {

    /**
     * @Route("/search", name="search_route")
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


}
