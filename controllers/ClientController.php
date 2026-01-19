<?php

class ClientController
{
    private $clientManager;

    public function __construct(ClientManager $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    public function handle(string $method, ?string $id): void
    {
        // cas GET/Clients -> On les lits tous
        if ($method === 'GET' && $id === null) {
            $clients = $this->clientManager->readAllClient();

            $out = [];
            foreach ($clients as $c) {
                $out[] = [
                    'clientID' => $c->getClientID(),
                    'prenom'   => $c->getPrenom(),
                    'nom'      => $c->getNom(),
                    'email'    => $c->getEmail(),
                    'toName'   => $c->getToName(),
                ];
            }

            $this->jsonResponse($out);
        }

        // cas GET/CLIENTS/ID on va chercher l'ID concerné
        if ($method === 'GET' && $id !== null) {
            $client = $this->clientManager->readClient((int)$id);

            if ($client === null) {
                $this->jsonResponse(['error' => 'Client not found'], 404);
            }

            $this->jsonResponse([
                'clientID' => $client->getClientID(),
                'prenom'   => $client->getPrenom(),
                'nom'      => $client->getNom(),
                'email'    => $client->getEmail(),
                'toName'   => $client->getToName(),
            ]);
        }

        // cas POST client, 
        if ($method === 'POST' && $id === null) {

            $body = $this->getJsonBody();

            // Si tout est bien claire, on trim et prepare.
            $prenom = isset($body['prenom']) ? trim((string)$body['prenom']) : '';
            $nom    = isset($body['nom']) ? trim((string)$body['nom']) : '';
            $email  = isset($body['email']) ? trim((string)$body['email']) : '';

            // Si un des champs est vide ( au cas ou il y ait un formulaire a terme. ) on renvoi un erreur.
            if ($prenom === '' || $nom === '' || $email === '') {
                $this->jsonResponse(['error' => 'prenom, nom, email sont requis'], 400);
            }

            // On créé un nouvel objet client qu'on rempli avec les infos et l'ajoute
            $client = new Client();
            $client->setPrenom($prenom);
            $client->setNom($nom);
            $client->setEmail($email);

            $newId = $this->clientManager->createClient($client);
            $this->jsonResponse(['status' => 'CREATED', 'clientID' => (int)$newId], 201);
        }

        // Cas PUT/clients/ID pour modifié un client
        if ($method === 'PUT' && $id !== null) {
            $id = (int)$id;
            $body = $this->getJsonBody();

            if (in_array($id, [1, 2], true)) {
                $this->jsonResponse([
                    "status" => "FORBIDDEN",
                    "message" => "ID protégé"
                ], 403);
            }

            $client = $this->clientManager->readClient((int)$id);
            if ($client === null) {
                $this->jsonResponse(['error' => 'Client not found'], 404);
            }


            if (isset($body['prenom'])) $client->setPrenom(trim((string)$body['prenom']));
            if (isset($body['nom']))    $client->setNom(trim((string)$body['nom']));
            if (isset($body['email']))  $client->setEmail(trim((string)$body['email']));

            $this->clientManager->updateClient($client);

            $this->jsonResponse(['status' => 'UPDATED']);
        }

        // Cas Suppression
        if ($method === 'DELETE' && $id !== null) {
            $id = (int)$id;
            if (in_array($id, [1, 2], true)) {
                $this->jsonResponse([
                    "status" => "FORBIDDEN",
                    "message" => "ID protégé"
                ], 403);
            }
            $this->clientManager->deleteClient((int)$id);
            $this->jsonResponse(['status' => 'DELETED']);
        }

        $this->jsonResponse(['error' => 'Not found'], 404);
    }

    private function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!$raw) return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
