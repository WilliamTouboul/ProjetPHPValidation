<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/Database.php';

// Models
require_once __DIR__ . '/models/Client.php';
require_once __DIR__ . '/models/Voyage.php';
require_once __DIR__ . '/models/Avis.php';

// Managers
require_once __DIR__ . '/manager/ClientManager.php';
require_once __DIR__ . '/manager/VoyageManager.php';
require_once __DIR__ . '/manager/AvisManager.php';

// Controllers
require_once __DIR__ . '/controllers/ClientController.php';
require_once __DIR__ . '/controllers/VoyageController.php';
require_once __DIR__ . '/controllers/AvisController.php';

function jsonResponse($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    $cnx = Database::getConnection();

    // Managers
    $clientManager = new ClientManager($cnx);
    $voyageManager = new VoyageManager($cnx);
    $avisManager   = new AvisManager($cnx);

    // Controllers
    $clientController = new ClientController($clientManager);
    $voyageController = new VoyageController($voyageManager);
    $avisController   = new AvisController($avisManager);

    $method = $_SERVER['REQUEST_METHOD'];

    // URI propre (sans query string)
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Base path (ex: /edsa-CertificationPHP)
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($base && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }

    $uri = '/' . ltrim($uri, '/');

    // Découpe /clients/3 => ["clients","3"]
    $parts = array_values(array_filter(explode('/', $uri)));

    // Route racine
    if (count($parts) === 0) {
        jsonResponse(['status' => 'OK', 'message' => 'API online']);
    }

    $resource = $parts[0];
    $id = $parts[1] ?? null;

    // Dispatch vers le bon controller
    switch ($resource) {
        case 'clients':
            $clientController->handle($method, $id);
            break;

        case 'voyages':
            $voyageController->handle($method, $id);
            break;

        case 'avis':
            $avisController->handle($method, $id);
            break;

        default:
            jsonResponse(['error' => 'Unknown resource'], 404);
    }
} catch (Throwable $e) {
    jsonResponse([
        'status'  => 'ERROR',
        'message' => 'Erreur serveur',
        'details' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine()
    ], 500);
}
