<?php

class AvisManager
{

    private $cnx;

    public function __construct($cnx)
    {
        $this->setCnx($cnx);
    }

    public function createAvis(Avis $avis)
    {
        // Preparation de la requete
        $sql = 'INSERT INTO avis
                (avis, voyageID, clientID, toID) 
                VALUES (:avis, :voyageID, :clientID, :toID)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':avis', $avis->getAvisContent(), PDO::PARAM_STR);
        $req->bindValue(':voyageID', $avis->getVoyageID(), PDO::PARAM_INT);
        $req->bindValue(':clientID', $avis->getClientID(), PDO::PARAM_INT);
        $req->bindValue(':toID', 1, PDO::PARAM_INT);

        $req->execute();

        // Return du dernier ID ajouté pour validation via 201
        return $this->cnx->lastInsertId();
    }

    public function readAllAvis()
    {
        $sql = 'SELECT a.avisID,
                   a.avis AS avisContent,
                   a.voyageID,
                   a.clientID,

                   c.prenom AS clientPrenom,
                   c.nom AS clientNom,
                   c.email AS clientEmail,

                   v.titre AS voyageTitre,
                   v.description AS voyageDescription,

                   t.name AS tourOperatorName
            FROM avis a
            JOIN client c ON c.clientID = a.clientID
            JOIN voyage v ON v.voyageID = a.voyageID
            JOIN tourOperator t ON t.toID = a.toID
            WHERE a.toID = 1
            ORDER BY a.avisID ASC';

        $req = $this->cnx->prepare($sql);
        $req->execute();

        $avisArray = [];

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $avis = new Avis();

            $avis->setAvisID($data['avisID']);
            $avis->setAvisContent($data['avisContent']);

            $avis->setVoyageID($data['voyageID']);
            $avis->setClientID($data['clientID']);

            // Infos enrichies
            $avis->setClientPrenom($data['clientPrenom']);
            $avis->setClientNom($data['clientNom']);
            $avis->setClientEmail($data['clientEmail']);

            $avis->setVoyageTitre($data['voyageTitre']);
            $avis->setVoyageDescription($data['voyageDescription']);

            $avis->setTourOperatorName($data['tourOperatorName']);

            $avisArray[] = $avis;
        }

        return $avisArray;
    }


    public function readAllAvisByVoyage(int $voyageID)
    {
        $sql = 'SELECT a.avisID,
                a.avis AS avisContent,
                a.voyageID,
                a.clientID,

                c.prenom AS clientPrenom,
                c.nom AS clientNom,
                c.email AS clientEmail,

                v.titre AS voyageTitre,
                v.description AS voyageDescription,

                t.name AS tourOperatorName
                FROM avis a
                JOIN client c ON c.clientID = a.clientID
                JOIN voyage v ON v.voyageID = a.voyageID
                JOIN tourOperator t ON t.toID = a.toID
                WHERE a.toID = 1
                AND a.voyageID = :voyageID
                ORDER BY a.avisID ASC';

        $req = $this->cnx->prepare($sql);
        $req->bindValue(':voyageID', $voyageID, PDO::PARAM_INT);
        $req->execute();

        $avisArray = [];

        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $avis = new Avis();

            $avis->setAvisID($data['avisID']);
            $avis->setAvisContent($data['avisContent']);
            $avis->setVoyageID($data['voyageID']);
            $avis->setClientID($data['clientID']);

            $avis->setClientPrenom($data['clientPrenom']);
            $avis->setClientNom($data['clientNom']);
            $avis->setClientEmail($data['clientEmail']);

            $avis->setVoyageTitre($data['voyageTitre']);
            $avis->setVoyageDescription($data['voyageDescription']);

            $avis->setTourOperatorName($data['tourOperatorName']);

            $avisArray[] = $avis;
        }

        return $avisArray;
    }


    public function updateAvis(Avis $avis)
    {
        // Modification d'un client
        $sql = 'UPDATE avis
                SET avis = :avis, voyageID = :voyageID
                WHERE avisID = :avisID
                AND toID = 1
                AND avisID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':avisID', $avis->getAvisID(), PDO::PARAM_INT);
        $req->bindValue(':avis', $avis->getAvisContent(), PDO::PARAM_STR);
        $req->bindValue(':voyageID', $avis->getVoyageID(), PDO::PARAM_INT);

        $req->execute();
    }

    public function deleteAvis(int $avisID)
    {
        $sql = 'DELETE FROM avis
                WHERE avisID = :avisID
                AND toID = 1
                AND avisID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':avisID', $avisID, PDO::PARAM_INT);

        $req->execute();
    }


    private function setCnx(PDO $cnx)
    {
        $this->cnx = $cnx;
    }
}
