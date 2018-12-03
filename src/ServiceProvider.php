<?php

namespace Fidelize\GraphQLAuthorizedIntrospection;

use Rebing\GraphQL\GraphQLServiceProvider;

class ServiceProvider extends GraphQLServiceProvider
{
    public function registerGraphQL()
    {
        $this->app->singleton('graphql', function($app) {
            $graphql = new GraphQL($app);
            $this->applySecurityRules();
            return $graphql;
        });
    }
}
