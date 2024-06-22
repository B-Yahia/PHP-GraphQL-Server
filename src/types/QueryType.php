<?php

declare(strict_types=1);

namespace Types;

use Config\Database;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{

    public function __construct(private Database $db)
    {
        $config = [
            'fields' => [
                'posts' => [
                    'type' => Type::listOf(new PostType()),
                    'description' => 'list of posts',
                    'resolve' => function ($root, $arg) use ($db) {
                        return $db->query('SELECT id, title, content, author FROM posts')->findAll();
                    }
                ],
                'post' => [
                    'type' => new PostType(),
                    'description' => 'Get post by ID',
                    'args' => [
                        'id' => Type::nonNull(Type::int()),
                    ],
                    'resolve' => function ($root, $args) use ($db) {
                        return $db->query('SELECT * FROM posts WHERE id=:id', ['id' => $args['id']])->find();
                    }
                ],
            ],
        ];

        parent::__construct($config);
    }
}
