<?php

declare(strict_types=1);

namespace Types;

use Config\Database;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType extends ObjectType
{
    public function __construct(private Database $db)
    {
        $config = [
            'fields' => [
                'addPost' => [
                    'type' => new PostType(),
                    'args' => [
                        'title' => ['type' => Type::nonNull(Type::string())],
                        'content' => ['type' => Type::nonNull(Type::string())],
                        'author' => ['type' => Type::string()]
                    ],
                    'resolve' => function ($root, $args) use ($db) {
                        $id = $db->query('INSERT INTO posts (title, content, author) VALUES (:title, :content, :author)', [
                            'title' => $args['title'],
                            'content' => $args['content'],
                            'author' => $args['author'] ?? 'Anonymous'
                        ])->id();
                        return $db->query('SELECT * FROM posts WHERE id=:id', ['id' => $id])->find();
                    }
                ],
                'deletePost' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::int())]
                    ],
                    'resolve' => function ($root, $args) use ($db) {
                        return $db->query('DELETE FROM posts WHERE id = :id', ['id' => $args['id']]);
                    }
                ],
                'updatePost' => [
                    'type' => new PostType(),
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::int())],
                        'title' => ['type' => Type::nonNull(Type::string())],
                        'content' => ['type' => Type::nonNull(Type::string())],
                        'author' => ['type' => Type::nonNull(Type::string())]
                    ],
                    'resolve' => function ($root, $args) use ($db) {

                        $db->query('UPDATE posts SET title=:title , content=:content , author=:author WHERE id=:id', [
                            'title' => $args['title'],
                            'content' => $args['content'],
                            'author' => $args['author'],
                            'id' => $args['id']
                        ]);
                        return $db->query('SELECT * FROM posts WHERE id=:id', ['id' => $args['id']])->find();
                    }
                ]

            ],
        ];
        parent::__construct($config);
    }
}
