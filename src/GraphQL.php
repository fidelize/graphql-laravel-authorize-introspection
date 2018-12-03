<?php

namespace Fidelize\GraphQLAuthorizedIntrospection;

use GraphQL\Type\Definition\ObjectType;
use Rebing\GraphQL\GraphQL as BaseGraphQL;
use Rebing\GraphQL\Schema;
use Rebing\GraphQL\SchemaNotFound;

class GraphQL extends BaseGraphQL
{
    /**
     * Only check for introspection authorization if this is request
     * contains an introspection query.
     * @return bool
     */
    private function isIntrospectionQueryRequest()
    {
        return request() && preg_match(
            '/\s*__(type|schema|typekind|field|directive|directivelocation|inputvalue|enumvalue)\s*\(/i',
            request()->__toString()
        );
    }

    /**
     * @param Schema|array|string|null $schema
     * @return Schema
     * @throws AuthorizeIntrospectionMethodNotImplemented
     */
    protected function getSchemaConfiguration($schema)
    {
        $schema = parent::getSchemaConfiguration($schema);

        if ($this->isIntrospectionQueryRequest()) {
            $visibilityFilter = function ($fieldClass) {
                $field = new $fieldClass();
                if (method_exists($field, 'authorizeIntrospection')) {
                    return $field->authorizeIntrospection();
                }
                throw new AuthorizeIntrospectionMethodNotImplemented(
                    "\"{$fieldClass}\" does not implement \"authorizeIntrospection\" method."
                );
            };
            foreach (['query', 'mutation', 'subscription'] as $type) {
                if (!empty($schema[$type])) {
                    $schema[$type] = array_filter($schema[$type], $visibilityFilter);
                }
            }
        }

        return $schema;
    }
}
