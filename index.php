<?php

require 'vendor/autoload.php';

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


$host = 'localhost';
$db   = 'GraphQLblog';
$user = 'username';
$pass = '123456';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$postType = new ObjectType([
    'name' => 'post',
    'description' => 'blog post',
    'fields' => [
        'id' => Type::nonNull(Type::int()),
        'title' => Type::nonNull(Type::string()),
        'content' => Type::nonNull(Type::string()),
        'author' => Type::string()
    ]
]);

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'posts' => [
            'type' => Type::listOf($postType),
            'description' => 'list of posts',
            'resolve' => function ($root, $arg) use ($pdo) {
                $stmt = $pdo->query('SELECT id, title, content, author FROM posts');
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        ],
        'post' => [
            'type' => $postType,
            'description' => 'Get post by ID',
            'args' => [
                'id' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($root, $args) use ($pdo) {
                $stmt = $pdo->prepare('SELECT * FROM posts WHERE id=:id');
                $stmt->execute(
                    ['id' => $args['id']]
                );
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        ],
    ],
]);

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'addPost' => [
            'type' => $postType,
            'args' => [
                'title' => ['type' => Type::nonNull(Type::string())],
                'content' => ['type' => Type::nonNull(Type::string())],
                'author' => ['type' => Type::string()]
            ],
            'resolve' => function ($root, $args) use ($pdo) {
                $stmt = $pdo->prepare('INSERT INTO posts (title, content, author) VALUES (:title, :content, :author)');
                $stmt->execute([
                    'title' => $args['title'],
                    'content' => $args['content'],
                    'author' => $args['author'] ?? 'Anonymous'
                ]);
                $args['id'] = $pdo->lastInsertId();
                return $args;
            }
        ],
        'deletePost' => [
            'type' => Type::boolean(),
            'args' => [
                'id' => ['type' => Type::nonNull(Type::int())]
            ],
            'resolve' => function ($root, $args) use ($pdo) {
                $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
                $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);
                return $stmt->execute();
            }
        ],
        'updatePost' => [
            'type' => $postType,
            'args' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'title' => ['type' => Type::string()],
                'content' => ['type' => Type::string()],
                'author' => ['type' => Type::string()]
            ],
            'resolve' => function ($root, $args) use ($pdo) {
                $fieldsToUpdate = [];
                if (isset($args['title'])) {
                    $fieldsToUpdate['title'] = $args['title'];
                }
                if (isset($args['content'])) {
                    $fieldsToUpdate['content'] = $args['content'];
                }
                if (isset($args['author'])) {
                    $fieldsToUpdate['author'] = $args['author'];
                }

                $sql = "UPDATE posts SET " . implode(', ', array_map(function ($field) {
                    return "$field = :$field";
                }, array_keys($fieldsToUpdate))) . " WHERE id = :id";

                $stmt = $pdo->prepare($sql);
                foreach ($fieldsToUpdate as $field => $value) {
                    $stmt->bindValue($field, $value);
                }
                $stmt->bindValue('id', $args['id'], PDO::PARAM_INT);
                $stmt->execute();

                return $args;
            }
        ],

    ],
]);

$schema = new Schema([
    'query' => $queryType,
    'mutation' => $mutationType
]);

try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
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
