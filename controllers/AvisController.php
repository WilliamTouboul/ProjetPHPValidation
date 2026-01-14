<?php
class AvisController
{
    private $avisManager;

    public function __construct(AvisManager $avisManager)
    {
        $this->avisManager = $avisManager;
    }

    public function handle(string $method, ?string $id): void
    {
        // GET /avis  ou  GET /avis?voyageID=1
        if ($method === 'GET' && $id === null) {
            $voyageID = isset($_GET['voyageID']) ? (int)$_GET['voyageID'] : null;

            if ($voyageID !== null && $voyageID > 0) {
                $avisList = $this->avisManager->readAllAvisByVoyage($voyageID);
            } else {
                $avisList = $this->avisManager->readAllAvis();
            }

            $out = [];
            foreach ($avisList as $a) {
                $out[] = [
                    'avisID'                => $a->getAvisID(),
                    'content'               => $a->getAvisContent(),
                    // 'voyageID'              => $a->getVoyageID(),
                    'voyageTitre'           => $a->getVoyageTitre(),
                    'voyageDescription'     => $a->getVoyageDescription(),
                    // 'clientID'              => $a->getClientID(),
                    'clientPrenom'          => $a->getClientPrenom(),
                    'clientNom'             => $a->getClientNom(),
                    'clientEmail'           => $a->getClientEmail(),
                    // 'toID'                  => $a->getToID(),
                    'tourOperatorName'      => $a->getTourOperatorName(),
                ];
            }

            $this->jsonResponse($out);
        }

        // POST /avis
        if ($method === 'POST' && $id === null) {
            $body = $this->getJsonBody();

            // Supporte ton renommage : avisContent / text
            $content = '';
            if (isset($body['avisContent'])) $content = trim((string)$body['avisContent']);
            if ($content === '' && isset($body['text'])) $content = trim((string)$body['text']);

            $voyageID = isset($body['voyageID']) ? (int)$body['voyageID'] : 0;
            $clientID = isset($body['clientID']) ? (int)$body['clientID'] : 0;

            if ($content === '' || $voyageID <= 0 || $clientID <= 0) {
                $this->jsonResponse(['error' => 'avisContent/text, voyageID et clientID sont requis'], 400);
            }

            $avis = new Avis();

            $avis->setAvisContent($content);


            $avis->setVoyageID($voyageID);
            $avis->setClientID($clientID);

            $newId = $this->avisManager->createAvis($avis);

            $this->jsonResponse(['status' => 'CREATED', 'avisID' => (int)$newId], 201);
        }

        // PUT /avis/{id}
        if ($method === 'PUT' && $id !== null) {
            $body = $this->getJsonBody();
            $avisID = (int)$id;

            $content = '';
            if (isset($body['avisContent'])) $content = trim((string)$body['avisContent']);
            if ($content === '' && isset($body['text'])) $content = trim((string)$body['text']);

            $voyageID = isset($body['voyageID']) ? (int)$body['voyageID'] : 0;

            if ($content === '' || $voyageID <= 0) {
                $this->jsonResponse(['error' => 'avisContent/text et voyageID sont requis'], 400);
            }

            $avis = new Avis();
            $avis->setAvisID($avisID);

            $avis->setAvisContent($content);


            $avis->setVoyageID($voyageID);

            $this->avisManager->updateAvis($avis);

            $this->jsonResponse(['status' => 'UPDATED']);
        }

        // DELETE /avis/{id}
        if ($method === 'DELETE' && $id !== null) {
            $this->avisManager->deleteAvis((int)$id);
            $this->jsonResponse(['status' => 'DELETED']);
        }

        $this->jsonResponse(['error' => 'Route avis not found'], 404);
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
