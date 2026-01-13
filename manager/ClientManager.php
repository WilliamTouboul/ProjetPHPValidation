<?php

class ClientManager
{

    // Variable pour la connection
    private $cnx;

    public function __construct($cnx)
    {
        $this->setCnx($cnx);
    }



    public function createClient(Client $client)
    {
        // Preparation de la requete
        $sql = 'INSERT INTO client
                (prenom, nom, email, toID) 
                VALUES (:prenom, :nom, :email, :toID)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':prenom', $client->getPrenom(), PDO::PARAM_STR);
        $req->bindValue(':nom', $client->getNom(), PDO::PARAM_STR);
        $req->bindValue(':email', $client->getEmail(), PDO::PARAM_STR);
        $req->bindValue(':toID', 1, PDO::PARAM_INT);

        $req->execute();

        // Return du dernier ID ajouté pour validation via 201
        return $this->cnx->lastInsertId();
    }

    public function readClient(int $clientID)
    {
        $sql = 'SELECT c.clientID, c.prenom, c.nom, c.email,
                t.name AS tourOperatorName
                FROM client c
                JOIN tourOperator t ON t.toID = c.toID
                WHERE c.clientID = :clientID
                AND c.toID = 1';

        $req = $this->cnx->prepare($sql);
        $req->bindValue(':clientID', $clientID, PDO::PARAM_INT);
        $req->execute();

        $data = $req->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $client = new Client();
        $client->setClientID($data['clientID']);
        $client->setPrenom($data['prenom']);
        $client->setNom($data['nom']);
        $client->setEmail($data['email']);
        $client->setToName($data['tourOperatorName']);


        return $client;
    }


    public function readAllClient()
    {
        // Lister TOUS les clients
        // REquete de tout les clients dans le scope "ToID=1"
        $sql = 'SELECT c.clientID, c.prenom, c.nom, c.email,
                t.name AS tourOperatorName
                FROM client c
                JOIN tourOperator t ON t.toID = c.toID
                WHERE c.toID = 1
                ORDER BY c.clientID ASC';

        $req = $this->cnx->prepare($sql);

        $req->execute();

        // Preparation du tableau
        $clients = [];

        // Boucle de création d'objet et d'un tableau les regroupant tous
        while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
            $client = new Client();
            $client->setClientID($data['clientID']);
            $client->setPrenom($data['prenom']);
            $client->setNom($data['nom']);
            $client->setEmail($data['email']);
            $client->setToName($data['tourOperatorName']);

            $clients[] = $client;
        }

        // Return du tableau
        return $clients;
    }


    // Pour UPDATE et DELETE : CRUD limité à toID=1 + interdiction update et delete sur les clients 1 & 2.

    public function updateClient(Client $client)
    {
        // Modification d'un client
        $sql = 'UPDATE client
                SET prenom = :prenom, nom = :nom, email = :email
                WHERE clientID = :clientID
                AND toID = 1
                AND clientID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':clientID', $client->getClientID(), PDO::PARAM_INT);
        $req->bindValue(':prenom', $client->getPrenom(), PDO::PARAM_STR);
        $req->bindValue(':nom', $client->getNom(), PDO::PARAM_STR);
        $req->bindValue(':email', $client->getEmail(), PDO::PARAM_STR);

        $req->execute();
    }

    public function deleteClient(int $clientID)
    {
        $sql = 'DELETE FROM client
                WHERE clientID = :clientID
                AND toID = 1
                AND clientID NOT IN (1, 2)';

        $req = $this->cnx->prepare($sql);

        $req->bindValue(':clientID', $clientID, PDO::PARAM_INT);

        $req->execute();
    }


    private function setCnx(PDO $cnx)
    {
        $this->cnx = $cnx;
    }
}
