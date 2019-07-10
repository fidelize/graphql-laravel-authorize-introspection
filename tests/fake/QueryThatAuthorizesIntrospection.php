<?php
namespace Fidelize\Tests\fake;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class QueryThatAuthorizesIntrospection extends Query
{
    protected $attributes = [
        'name' => 'version',
    ];

    public function type()
    {
        return Type::string();
    }

    public function resolve($root, $args)
    {
        return 'OK';
    }

    public function authorizeIntrospection()
    {
        return true;
    }
}
