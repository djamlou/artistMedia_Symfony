<?php
namespace App\DataFixtures\ORM;


use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class LoadArtistData extends Fixture
{

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $artists = [
            [
                'name' => 'Cabrel',
            ],
            [
                'name' => 'Joe Dassin',
            ],
            [
                'name' => 'REM',
            ],

        ];

        foreach ($artists as $artistData){
            $artist = new Artist();
            $artist->setName($artistData['name']);

            $manager->persist($artist);
            $this->addReference('artist_'.$artist->getName(), $artist);

        }
        $manager->flush();

    }
}