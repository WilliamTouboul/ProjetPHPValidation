<?php
class Voyage
{
    // Attributs
    private $voyageID;
    private $titre;
    private $description;
    private $toID;
    // Attributs pour enrichir avec les jointures
    private $tourOperatorName;


    /* --------------------------------- GETTER --------------------------------- */
    public function getVoyageID()
    {
        return $this->voyageID;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getToID()
    {
        return $this->toID;
    }

    public function getTourOperatorName()
    {
        return $this->tourOperatorName;
    }

    /* --------------------------------- SETTERS -------------------------------- */
    public function setVoyageID($voyageID)
    {
        $this->voyageID = $voyageID;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setToID($toID)
    {
        $this->toID = $toID;
    }

    public function setTourOperatorName(string $tourOperatorName)
    {
        $this->tourOperatorName = $tourOperatorName;
    }
}
