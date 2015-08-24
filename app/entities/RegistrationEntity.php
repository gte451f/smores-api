<?php
namespace PhalconRest\Entities;

use \PhalconRest\Util\ValidationException;

class RegistrationEntity extends \PhalconRest\API\Entity
{

    /**
     * extend to always provide account_id and school grade
     * these are values stored in the user table, yet only attendee is joined
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::queryBuilder()
     */
    // public function queryBuilder($count = false)
    // {
    // $query = parent::queryBuilder($count);
    
    // // no need to proceed for simple counts
    // if ($count) {
    // return $query;
    // }
    
    // $config = $this->getDI()->get('config');
    // $nameSpace = $config['namespaces']['models'];
    
    // $modelNameSpace = $nameSpace . $this->model->getModelName();
    // $refModelNameSpace = $nameSpace . 'Attendees';
    
    // $query->join($refModelNameSpace);
    // $columns = array(
    // "$modelNameSpace.*",
    // "$refModelNameSpace.account_id, $refModelNameSpace.school_grade"
    // );
    // $query->columns($columns);
    
    // return $query;
    // }
    
    /**
     * everytime a new registration is created
     * consult with the fee table and create relevant charges
     * this could be a registration fee
     *
     * should we apply the "first time" fee for a newly registered camper
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterSave()
     */
    public function afterSave($object, $id)
    {
        // only apply system wide fees on insert
        if ($this->saveMode == 'update') {
            return;
        }
        
        // pull system fees based around registraton
        $regFees = \PhalconRest\Models\Fees::find(array(
            "basis = 'Registration'"
        ));
        
        $attendee = \PhalconRest\Models\Attendees::findFirst($object->attendee_id);
        
        if ($attendee) {
            // create a charge fee for each one found
            foreach ($regFees as $fee) {
                $charge = new \PhalconRest\Models\Charges();
                $charge->registration_id = $id;
                $charge->name = $fee->name;
                $charge->amount = $fee->amount;
                $charge->account_id = $attendee->account_id;
                
                if ($charge->create() == false) {
                    throw new ValidationException("Internal error creating a registration", array(
                        'code' => '34534657',
                        'dev' => 'Error while processing RegistrationEntity->afterSave().  Could not create Charge record.'
                    ), $charge->getMessages());
                }
            }
        } else {
            throw new ValidationException("Internal error creating a registration", array(
                'code' => '4562456786',
                'dev' => 'Error while processing RegistratinoEntity->afterSave(). Could not find a valid attendee record.'
            ), $charge->getMessages());
        }
    }
}