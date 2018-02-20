<?php
namespace App\Controller;

use App\Entity\Track;
use App\Form\SearchFormTrackType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrackController extends Controller{

    /**
     *@Route ("/track/{id}", name="show_track_route", requirements={"id": "[0-9]+"})
     */
    public function showAction(Track $track)
    {
        return $this->render('track/track.html.twig',['track' => $track]);
    }

    /**
     * @Route("/track/search", name="search_track_route")
     */
    public function searchTrack(Request $request){
        $form = $this->createForm(SearchFormTrackType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine()->getRepository(Track::class);
            $track = $doctrine->findOneBytitle($form['title']->getData());
            return $this->redirectToRoute('show_track_route', ['id' => $track->getId()]);

        }
        return $this->render('track/search.html.twig', [
            'searchForm' => $form->createView()]);
    }

}