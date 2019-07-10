<?php
namespace Fidelize\Tests;

use Fidelize\GraphQLAuthorizedIntrospection\AuthorizeIntrospectionMethodNotImplemented;
use Fidelize\GraphQLAuthorizedIntrospection\GraphQL;
use GraphQL\Type\Definition\Type;
use Illuminate\Http\Request;
use Fidelize\Tests\fake\QueryThatDoesNotAuthorizeIntrospection;
use Fidelize\Tests\fake\QueryThatAuthorizesIntrospection;
use Fidelize\Tests\fake\QueryThatDoesNotHaveAuthorizeIntrospectionMethod;

class GraphQLTest extends TestCase
{
    public function testGetSchemaConfigurationDoesNothingIfRequestIsNotIntrospection()
    {
        $graphql = new GraphQL($this->app);
        $graphql->addSchema('default', [
            'query' => [
                'version' => [
                    'name' => 'version',
                    'type' => Type::string(),
                    'resolve' => function () { return 'OK'; },
                ]
            ]
        ]);
        $schema = $graphql->schema();
        $this->assertNotNull($schema->getQueryType()->getField('version'));
    }

    public function testGetSchemaConfigurationRemoveUnauthorizedQueries()
    {
        $graphql = new GraphQL($this->app);
        $request = new Request(
            $query = [],
            $request = [],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = [],
            $content = '__type(name: "Query") { name }'
        );
        $this->app->instance('request', $request);
        $graphql->addSchema('default', [
            'query' => [
                'version' => QueryThatDoesNotAuthorizeIntrospection::class,
            ]
        ]);
        $schema = $graphql->schema();
        $fields = $schema->getQueryType()->getFields();
        $this->assertTrue(empty($fields['version']));
    }

    public function testGetSchemaFailsIfOneQueryDoesNotImplementAuthorizeIntrospection()
    {
        $this->expectException(AuthorizeIntrospectionMethodNotImplemented::class);

        $graphql = new GraphQL($this->app);
        $request = new Request(
            $query = [],
            $request = [],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = [],
            $content = '__type(name: "Query") { name }'
        );
        $this->app->instance('request', $request);
        $graphql->addSchema('default', [
            'query' => [
                'version' => QueryThatDoesNotHaveAuthorizeIntrospectionMethod::class,
            ]
        ]);
        $graphql->schema();
    }

    public function testGetSchemaConfigurationKeepsAuthorizedQueries()
    {
        $graphql = new GraphQL($this->app);
        $request = new Request(
            $query = [],
            $request = [],
            $attributes = [],
            $cookies = [],
            $files = [],
            $server = [],
            $content = '__type(name: "Query") { name }'
        );
        $this->app->instance('request', $request);
        $graphql->addSchema('default', [
            'query' => [
                'version' => QueryThatAuthorizesIntrospection::class,
            ]
        ]);
        $schema = $graphql->schema();
        $this->assertNotNull($schema->getQueryType()->getField('version'));
    }
}
