<?php
namespace App\DataFixtures\ORM;


use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class LoadAlbumData extends Fixture
{

    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $albums = [
            [
                'title' => 'Sarbacane',
                'releaseDate' => '1989',
                'artist' => 'Cabrel',
            ],
            [
                'title' => 'Petite Marie',
                'releaseDate' => '1977',
                'artist' => 'Cabrel',
            ],
            [
                'title' => 'L\'été indien',
                'releaseDate' => '1975',
                'artist' => 'Joe Dassin',
            ],
            [
                'title' => 'A toi',
                'releaseDate' => '1977',
                'artist' => 'Joe Dassin',
            ],
            [
                'title' => 'Around the Sun',
                'releaseDate' => '2004',
                'artist' => 'REM',
            ],
        ];


        foreach ($albums as $albumData){
            $album = new Album();
            $album->setTitle($albumData['title'])
                ->setReleaseDate($albumData['releaseDate']);

            if (!$this->hasReference('artist_'.$albumData['artist'])){
                continue;
            }
            $artist = $this->getReference('artist_'.$albumData['artist']);
            $album->setArtist($artist);

            $manager->persist($album);
            $this->addReference('album_'.$album->getTitle(), $album);

        }
        $manager->flush();
    }
}