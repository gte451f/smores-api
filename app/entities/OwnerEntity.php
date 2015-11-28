<?php
namespace PhalconRest\Entities;

class OwnerEntity extends \PhalconRest\API\Entity
{
    /**
     * auto assign user_type to form
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::beforeSave()
     */
    public function beforeSave($object, $id)
    {
        // extend me in child class
        $object->user_type = 'Owner';
    
        return $object;
    }
}