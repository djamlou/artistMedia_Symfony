<?php
namespace App\Controller;

use App\Entity\Track;
use App\Form\SearchFormTrackType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
     * @Route("/api/track", name="rest_track")
     * @Method({"GET"})
     */
    public function getTracksREST(Request $request){

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $em = $this->getDoctrine()->getRepository(Track::class);
        $tracks = $em->findAll();

        $normalizer->setCircularReferenceHandler(
            function ($object){
                return $object->getId();
            }
        );

        $options = array( "attributes"=>array('id', 'title', 'number',
            'duration'=>array('timestamp'),
            'lyrics', 'albumId'));
        $rep = new Response(
            $serializer->serialize($tracks, 'json', $options)
        );
        $rep->headers->set('Access-Control-Allow-Origin', '*');
        return $rep;

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