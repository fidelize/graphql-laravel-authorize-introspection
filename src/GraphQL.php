<?php

namespace Fidelize\GraphQLAuthorizedIntrospection;

use Rebing\GraphQL\GraphQL as BaseGraphQL;
use Rebing\GraphQL\Schema;

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
            '/\s*__(type|schema|typekind|field|directive|directivelocation|inputvalue|enumvalue)\s*(\(|\{)/i',
            request()->__toString()
        );
    }

    /**
     * @param  array|string|null  $schema
     * @return array
     * @throws AuthorizeIntrospectionMethodNotImplemented
     */
    protected function getSchemaConfiguration($schema): array
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
