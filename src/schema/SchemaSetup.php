<?php

declare(strict_types=1);

namespace Schema;

use Config\Database;
use GraphQL\Type\Schema;
use Types\MutationType;
use Types\QueryType;

class SchemaSetup
{
    public static function getSchema(Database $db)
    {
        return new Schema([
            'query' => new QueryType($db),
            'mutation' => new MutationType($db),
        ]);
    }
}
