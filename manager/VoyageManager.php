<?php

class VoyageManager
{

    private $cnx;

    public function __construct($cnx)
    {
        $this->setCnx($cnx);
    }


    public function createVoyage(Voyage $voyage)
    {
        $sql = 'INSERT INTO voyage
                (titre, description, toID) 
                VALUES (:titre, :description, :toID)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':titre', $voyage->getTitre(), PDO::PARAM_STR);
        $req->bindValue(':description', $voyage->getDescription(), PDO::PARAM_STR);
        $req->bindValue(':toID', 1, PDO::PARAM_INT);

        $req->execute();

        // Return du dernier ID ajouté pour validation via 201
        return $this->cnx->lastInsertId();
    }

    public function readVoyage(int $voyageID)
    {
        $sql = 'SELECT v.voyageID, v.titre, v.description,
            t.name AS tourOperatorName
            FROM voyage v
            JOIN tourOperator t ON t.toID = v.toID
            WHERE v.voyageID = :voyageID
            AND v.toID = 1';

        $req = $this->cnx->prepare($sql);
        $req->bindValue(':voyageID', $voyageID, PDO::PARAM_INT);
        $req->execute();

        $data = $req->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $voyage = new Voyage();
        $voyage->setVoyageID($data['voyageID']);
        $voyage->setTitre($data['titre']);
        $voyage->setDescription($data['description']);
        $voyage->setTourOperatorName($data['tourOperatorName']);

        return $voyage;
    }


    public function readAllVoyage()
    {
        $sql = 'SELECT v.voyageID, v.titre, v.description,
                t.name AS tourOperatorName
                FROM voyage v
                JOIN tourOperator t ON t.toID = v.toID
                WHERE v.toID = 1
                ORDER BY v.voyageID ASC';

        $req = $this->cnx->prepare($sql);
        $req->execute();

        $voyages = [];

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $voyage = new Voyage();
            $voyage->setVoyageID($data['voyageID']);
            $voyage->setTitre($data['titre']);
            $voyage->setDescription($data['description']);
            $voyage->setTourOperatorName($data['tourOperatorName']);

            $voyages[] = $voyage;
        }

        return $voyages;
    }


    public function updateVoyage(Voyage $voyage)
    {
        // Modification d'un voyage
        $sql = 'UPDATE voyage
                SET titre = :titre, description = :description
                WHERE voyageID = :voyageID
                AND toID = 1
                AND voyageID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':voyageID', $voyage->getVoyageID(), PDO::PARAM_INT);
        $req->bindValue(':titre', $voyage->getTitre(), PDO::PARAM_STR);
        $req->bindValue(':description', $voyage->getDescription(), PDO::PARAM_STR);

        $req->execute();
    }

    public function deleteVoyage(int $voyageID)
    {
        $sql = 'DELETE FROM voyage
                WHERE voyageID = :voyageID
                AND toID = 1
                AND voyageID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':voyageID', $voyageID, PDO::PARAM_INT);

        $req->execute();
    }


    private function setCnx(PDO $cnx)
    {
        $this->cnx = $cnx;
    }
}
