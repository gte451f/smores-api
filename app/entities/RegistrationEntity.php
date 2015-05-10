<?php
namespace PhalconRest\Entities;

class RegistrationEntity extends \PhalconRest\API\Entity
{

    /**
     * extend to always provide account_id
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::queryBuilder()
     */
    public function queryBuilder($count = false)
    {
        $query = parent::queryBuilder($count);
        
        $config = $this->getDI()->get('config');
        $nameSpace = $config['namespaces']['models'];
        
        $modelNameSpace = $nameSpace . $this->model->getModelName();
        $refModelNameSpace = $nameSpace . 'Attendees';
        
        $query->join($refModelNameSpace);
        $columns = array(
            "$modelNameSpace.*",
            "$refModelNameSpace.account_id, $refModelNameSpace.school_grade"
        );
        $query->columns($columns);
        
        return $query;
    }
}