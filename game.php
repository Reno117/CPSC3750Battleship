<?php
session_start();
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

// New Game
if (isset($input['action']) && $input['action'] === 'newGame') {
    $_SESSION = []; // clear session
    echo json_encode(['status' => 'reset']);
    exit;
}

// Initialize session state
if (!isset($_SESSION['playerShips'])) {
    $_SESSION['playerShips'] = [];
    $_SESSION['aiShips'] = [];
    while (count($_SESSION['playerShips']) < 5) $_SESSION['playerShips'][rand(0,63)] = true;
    while (count($_SESSION['aiShips']) < 5) $_SESSION['aiShips'][rand(0,63)] = true;
    $_SESSION['playerShots'] = [];
    $_SESSION['aiShots'] = [];
}

// Player shot
$shot = $input['shot'];
$result = 'miss';
if (isset($_SESSION['aiShips'][$shot])) {
    $result = 'hit';
    unset($_SESSION['aiShips'][$shot]);
}
$_SESSION['playerShots'][$shot] = $result;

// Check game over
$gameOver = empty($_SESSION['aiShips']);

// AI turn if game not over
$aiShotData = null;
if (!$gameOver) {
    do {
        $aiShot = rand(0,63);
    } while (isset($_SESSION['aiShots'][$aiShot]));
    $aiResult = isset($_SESSION['playerShips'][$aiShot]) ? 'hit' : 'miss';
    $_SESSION['aiShots'][$aiShot] = $aiResult;
    if ($aiResult === 'hit') unset($_SESSION['playerShips'][$aiShot]);
    $aiShotData = ['index' => $aiShot, 'result' => $aiResult];
    $aiGameOver = empty($_SESSION['playerShips']);
}

echo json_encode([
    'result' => $result,
    'gameOver' => $gameOver,
    'aiShot' => $aiShotData ?? null,
    'aiGameOver' => $aiGameOver ?? false
]);
