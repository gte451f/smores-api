<?php
namespace PhalconRest\Entities;

use \PhalconRest\Libraries\CustomFields\Util;

class FieldEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * security measure to prevent side loading any other data from this end point
     */
    public function configureSearchHelper()
    {
        $this->searchHelper->entityWith = 'block';
    }

    /**
     * rebuild the view after each delete
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::afterDelete()
     */
    public function afterDelete($model)
    {
        Util::rebuildView($model->table);
    }

    /**
     * override default afterQueryBUilderHook since this entity is not concerned with account level security
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\API\Entity::afterQueryBuilderHook()
     */
    public function afterQueryBuilderHook($query)
    {
        return $query;
    }
}