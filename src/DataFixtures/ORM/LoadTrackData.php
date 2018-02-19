<?php
namespace App\DataFixtures\ORM;


use App\Entity\Track;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class LoadTrackData extends Fixture
{

    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        $tracks = [
            [
                'number' => '1',
                'title' => 'Animal',
                'duration' => '0:5:28',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '2',
                'title' => 'C\'est écrit',
                'duration' => '0:5:54',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '3',
                'title' => 'Sarbacane',
                'duration' => '0:4:08',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '4',
                'title' => 'Rosie',
                'duration' => '0:3:49',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '5',
                'title' => 'Tout le monde y pense',
                'duration' => '0:4:04',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '6',
                'title' => 'Je sais que tu danses',
                'duration' => '0:4:52',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '7',
                'title' => 'j\'ai peur de l\'avion',
                'duration' => '0:3:55',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '8',
                'title' => 'Dormir debout ',
                'duration' => '0:4:49',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '9',
                'title' => 'Petite sirène',
                'duration' => '0:3:47',
                'album' => 'Sarbacane',
            ],
            [
                'number' => '10',
                'title' => 'Le pas des ballerines',
                'duration' => '0:6:19',
                'album' => 'Sarbacane',
            ]

        ];


        foreach ($tracks as $trackData) {
            $track = new Track();
            $track->setNumber($trackData['number'])
                ->setTitle($trackData['title'])
                ->setDuration(new \DateTime($trackData['duration']));

            if (!$this->hasReference('album_' . $trackData['album'])) {
                continue;
            }
            $album = $this->getReference('album_' . $trackData['album']);
            $track->setAlbum($album);

            $manager->persist($track);

        }
        $manager->flush();

    }
}