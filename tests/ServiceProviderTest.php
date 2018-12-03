<?php
namespace Tests;

use Fidelize\GraphQLAuthorizedIntrospection\GraphQL;
use Fidelize\GraphQLAuthorizedIntrospection\ServiceProvider;
use Mockery;

class ServiceProviderTest extends TestCase
{
    public function testItRegistersModifiedGraphQLClass()
    {
        $provider = new ServiceProvider($this->app);
        $provider->register();
        $graphql = $this->app->make('graphql');
        $this->assertInstanceOf(GraphQL::class, $graphql);
    }
}
