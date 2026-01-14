<?php
class Client
{
    // Attributs
    private $clientID;
    private $prenom;
    private $nom;
    private $email;
    private $toID;
    // Attributs pour enrichir aprés jointures
    private $toName;

    /* --------------------------------- GETTER --------------------------------- */
    public function getClientID()
    {
        return $this->clientID;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getToID()
    {
        return $this->toID;
    }

    public function getToName()
    {
        return $this->toName;
    }

    /* --------------------------------- SETTERS -------------------------------- */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setToID($toID)
    {
        $this->toID = $toID;
    }
    public function setToName($toName)
    {
        $this->toName = $toName;
    }
}
