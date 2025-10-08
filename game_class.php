<?php
class Game
{
    private $id;
    private $title;
    private $developer;
    private $description;
    private $genre;
    private $platform;
    private $releaseYear;
    private $rating;

    # Constructor: volgorde moet overeenkomen met je GameManager
    //construct wordt direct uitgevoerd als het object wordt aangemaakt
    // $game = new Game();
    public function __construct(
        $title,
        $developer,
        $description,
        $genre,
        $platform,
        $releaseYear,
        $rating,
        $id = null
    ) {
        $this->title       = $title;
        $this->developer   = $developer;
        $this->description = $description;
        $this->genre       = $genre;
        $this->platform    = $platform;
        $this->releaseYear = $releaseYear;
        $this->rating      = $rating;
        $this->id          = $id;
    }

    # Getters
    public function getId()
    {
        return $this->id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDeveloper()
    {
        return $this->developer;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getGenre()
    {
        return $this->genre;
    }
    public function getPlatform()
    {
        return $this->platform;
    }
    public function getReleaseYear()
    {
        return $this->releaseYear;
    }
    public function getRating()
    {
        return $this->rating;
    }

    # Setters (optioneel, handig bij update)
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function setDeveloper($developer)
    {
        $this->developer = $developer;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }
    public function setReleaseYear($releaseYear)
    {
        $this->releaseYear = $releaseYear;
    }
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
}
