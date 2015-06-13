<?php
namespace PhalconRest\Entities;

class EmployeeEntity extends \PhalconRest\API\Entity
{

    /**
     * most direct way to customize the result set
     * add a fake ID field to the result set since an ID is required by Ember Data
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::filterFields()
     */
    protected function filterFields($baseArray)
    {
        $baseArray['id'] = $baseArray['user_id'];
        return parent::filterFields($baseArray);
    }

    /**
     * prevent some fields from appearing in the result set
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::configureSearchHelper()
     */
    final public function configureSearchHelper()
    {
        $this->searchHelper->entityBlockFields[] = 'password';
        $this->searchHelper->entityBlockFields[] = 'salt';
    }

    /**
     * protect existing password if no new password is provided
     * never allow a new salt to be set
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::simpleSave()
     */
    function simpleSave($model, $object)
    {
        $foo = 1;
        if (strlen($object->password) == 0) {
            $object->password = $model->password;
        }
        $object->salt = $model->salt;
        
        return parent::simpleSave($model, $object);
    }
}