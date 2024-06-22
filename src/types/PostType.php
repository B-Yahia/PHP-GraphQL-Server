<?php

declare(strict_types=1);

namespace Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PostType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'description' => 'A blog post',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'title' => Type::nonNull(Type::string()),
                'content' => Type::nonNull(Type::string()),
                'author' => Type::string()
            ]
        ];
        parent::__construct($config);
    }
}
