<?php
class Avis
{
    // Attributs
    private $avisID;
    private $text;
    private $voyageID;
    private $clientID;
    private $toID;
    // Attributs pour enrichissement aprés jointures 
    private $clientPrenom;
    private $clientNom;
    private $clientEmail;
    private $voyageTitre;
    private $voyageDescription;
    private $tourOperatorName;

    /* --------------------------------- GETTERS -------------------------------- */
    public function getAvisID()
    {
        return $this->avisID;
    }

    public function getAvisContent()
    {
        return $this->text;
    }

    public function getVoyageID()
    {
        return $this->voyageID;
    }

    public function getClientID()
    {
        return $this->clientID;
    }

    public function getToID()
    {
        return $this->toID;
    }

    public function getClientPrenom()
    {
        return $this->clientPrenom;
    }
    public function getClientNom()
    {
        return $this->clientNom;
    }

    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    public function getVoyageTitre()
    {
        return $this->voyageTitre;
    }
    public function getVoyageDescription()
    {
        return $this->voyageDescription;
    }
    public function getTourOperatorName()
    {
        return $this->tourOperatorName;
    }

    /* --------------------------------- SETTERS -------------------------------- */
    public function setAvisID($avisID)
    {
        $this->avisID = $avisID;
    }

    public function setAvisContent($text)
    {
        $this->text = $text;
    }

    public function setVoyageID($voyageID)
    {
        $this->voyageID = $voyageID;
    }

    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
    }

    public function setToID($toID)
    {
        $this->toID = $toID;
    }

    // Setters pour les jointures

    public function setClientPrenom($clientPrenom)
    {
        $this->clientPrenom = $clientPrenom;
    }
    public function setClientNom($clientNom)
    {
        $this->clientNom = $clientNom;
    }
    public function setClientEmail($clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }
    public function setVoyageTitre($voyageTitre)
    {
        $this->voyageTitre = $voyageTitre;
    }
    public function setVoyageDescription($voyageDescription)
    {
        $this->voyageDescription = $voyageDescription;
    }
    public function setTourOperatorName($tourOperatorName)
    {
        $this->tourOperatorName = $tourOperatorName;
    }
}
