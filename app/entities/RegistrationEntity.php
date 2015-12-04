<?php
namespace PhalconRest\Entities;

use \PhalconRest\Util\ValidationException;

class RegistrationEntity extends \PhalconRest\Libraries\API\Entity
{

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

    /**
     * custom implimentation of account filter
     * must filter through related attendee records
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\API\Entity::applyAccountFilter()
     */
    public function applyAccountFilter($query)
    {
        // load current account
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        // add custom filter
        $query->where("PhalconRest\Models\Attendees.account_id = $currentUser->accountId");
        
        // only add needed join if it isn't already in place
        $applyJoin = true;
        foreach ($this->activeRelations as $alias => $relation) {
            if ($alias == 'Attendees') {
                $applyJoin = false;
                break;
            }
        }
        if ($applyJoin) {
            $query->join("PhalconRest\Models\Attendees");
        }
        return $query;
    }
}