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
        // GET /clients
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

        // GET /clients/{id}
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

        // POST /clients
        if ($method === 'POST' && $id === null) {
            $body = $this->getJsonBody();

            $prenom = isset($body['prenom']) ? trim((string)$body['prenom']) : '';
            $nom    = isset($body['nom']) ? trim((string)$body['nom']) : '';
            $email  = isset($body['email']) ? trim((string)$body['email']) : '';

            if ($prenom === '' || $nom === '' || $email === '') {
                $this->jsonResponse(['error' => 'prenom, nom, email sont requis'], 400);
            }

            $client = new Client();
            $client->setPrenom($prenom);
            $client->setNom($nom);
            $client->setEmail($email);

            $newId = $this->clientManager->createClient($client);
            $this->jsonResponse(['status' => 'CREATED', 'clientID' => (int)$newId], 201);
        }

        // PUT /clients/{id}
        if ($method === 'PUT' && $id !== null) {
            $body = $this->getJsonBody();

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

        // DELETE /clients/{id}
        if ($method === 'DELETE' && $id !== null) {
            $this->clientManager->deleteClient((int)$id);
            $this->jsonResponse(['status' => 'DELETED']);
        }

        $this->jsonResponse(['error' => 'Not found'], 404);
    }

    private function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data);
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
