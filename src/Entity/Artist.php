<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArtistRepository")
 */
class Artist {

    /**
     * @var int
     *
     * @ORM\Id()
     *@ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
        private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\Length(min="3")
     */
        private $name;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Album", mappedBy="artist", cascade={"all"})
     */
        private $albums;
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", inversedBy="artists")
     */
        private $genres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;


    function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Album[]
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param Album[] $albums
     */
    public function setAlbums($albums)
    {
        $this->albums = $albums;
    }

    /**
     * @return Genre[]
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param Genre[] $genres
     * @return  Artist
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img)
    {
        $this->img = $img;
    }


}