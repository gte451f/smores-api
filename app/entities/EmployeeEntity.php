<?php
namespace PhalconRest\Entities;

class EmployeeEntity extends \PhalconRest\API\Entity
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
        $object->user_type = 'Employee';
        
        return $object;
    }
}