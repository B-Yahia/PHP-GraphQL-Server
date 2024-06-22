<?php

require 'vendor/autoload.php';

use Config\Setup;
use GraphQL\GraphQL;
use Schema\SchemaSetup;

Setup::cros('http://localhost:3000');

$db = Setup::database();

$schema = SchemaSetup::getSchema($db);

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $query = $input['query'];
    $variables = isset($input['variables']) ? $input['variables'] : [];
    $result = GraphQL::executeQuery($schema, $query, null, null, $variables)->toArray();
} catch (\Exception $e) {
    $result = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
