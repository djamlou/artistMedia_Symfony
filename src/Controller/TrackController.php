<?php
namespace App\Controller;

use App\Entity\Track;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrackController extends Controller{

    /**
     *@Route ("/track/{id}", requirements={"id": "[0-9]+"})
     */
    public function showAction(Track $track)
    {
        return $this->render('track/track.html.twig',['track' => $track]);
    }

}