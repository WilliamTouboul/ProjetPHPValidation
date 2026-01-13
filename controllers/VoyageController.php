<?php

class VoyageController
{
    private $voyageManager;

    public function __construct(VoyageManager $voyageManager)
    {
        $this->voyageManager = $voyageManager;
    }

    public function handle(string $method, ?string $id): void
    {
        // GET /voyages
        if ($method === 'GET' && $id === null) {
            $voyages = $this->voyageManager->readAllVoyage();

            $out = [];
            foreach ($voyages as $v) {
                $out[] = [
                    'voyageID'    => $v->getVoyageID(),
                    'titre'       => $v->getTitre(),
                    'description' => $v->getDescription(),
                    'toName'      => $v->getTourOperatorName(),
                ];
            }

            $this->jsonResponse($out);
        }

        // GET /voyages/{id}
        if ($method === 'GET' && $id !== null) {
            $voyage = $this->voyageManager->readVoyage((int)$id);

            if ($voyage === null) {
                $this->jsonResponse(['error' => 'Voyage not found'], 404);
            }

            $this->jsonResponse([
                'voyageID'    => $voyage->getVoyageID(),
                'titre'       => $voyage->getTitre(),
                'description' => $voyage->getDescription(),
                'toName'        => $voyage->getTourOperatorName(),
            ]);
        }

        // POST /voyages
        if ($method === 'POST' && $id === null) {
            $body = $this->getJsonBody();

            $titre = isset($body['titre']) ? trim((string)$body['titre']) : '';
            $description = isset($body['description']) ? trim((string)$body['description']) : '';

            if ($titre === '' || $description === '') {
                $this->jsonResponse(['error' => 'titre et description sont requis'], 400);
            }

            $voyage = new Voyage();
            $voyage->setTitre($titre);
            $voyage->setDescription($description);

            $newId = $this->voyageManager->createVoyage($voyage);

            $this->jsonResponse(['status' => 'CREATED', 'voyageID' => (int)$newId], 201);
        }

        // PUT /voyages/{id}
        if ($method === 'PUT' && $id !== null) {
            $body = $this->getJsonBody();

            $voyage = $this->voyageManager->readVoyage((int)$id);
            if ($voyage === null) {
                $this->jsonResponse(['error' => 'Voyage not found'], 404);
            }

            if (isset($body['titre'])) {
                $voyage->setTitre(trim((string)$body['titre']));
            }
            if (isset($body['description'])) {
                $voyage->setDescription(trim((string)$body['description']));
            }

            $this->voyageManager->updateVoyage($voyage);

            $this->jsonResponse(['status' => 'UPDATED']);
        }

        // DELETE /voyages/{id}
        if ($method === 'DELETE' && $id !== null) {
            $this->voyageManager->deleteVoyage((int)$id);
            $this->jsonResponse(['status' => 'DELETED']);
        }

        $this->jsonResponse(['error' => 'Route voyages not found'], 404);
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
