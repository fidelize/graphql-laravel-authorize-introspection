# Authorize Introspection

[rebing/graphql-laravel](https://github.com/rebing/graphql-laravel) lists all
queries, mutations and subscriptions when you perform
[introspection](https://facebook.github.io/graphql/June2018/#sec-Introspection),
even those queries which would not be authorized when called (due to rules in
their `#authorize` method).

This extension allows us to:

* Define separate rules for calling a query and introspecting a query.
* Only list allowed queries, mutations and subscriptions in an introspection.

For example: you may want to list `updatePost` mutation for all authors with
`authorizeIntrospection`, but only allow an author to edit his or her own post
on calling `updatePost`. Thus:

* `authorizeIntrospection`: allows showing the documentation.
* `authorize`: allows calling it with the given arguments.

In your queries, mutations and subscriptions base classes, you may want to add:

```php
<?php

namespace App\GraphQL\Mutation;

use Rebing\GraphQL\Support\Mutation;

class AbstractMutation extends Mutation
{
    public function authorizeIntrospection()
    {
        // Your rule here
        return true;
    }

    public function authorize(array $args)
    {
        // Only override when you have custom rule according to the $args
        return $this->authorizeIntrospection();
    }
}
```

## Installation

`composer require "fidelize/graphql-laravel-authorize-introspection"`

Replace `Rebing\GraphQL\GraphQLServiceProvider` with
`Fidelize\GraphQLAuthorizedIntrospection\ServiceProvider` in your
`config/app.php` file.
