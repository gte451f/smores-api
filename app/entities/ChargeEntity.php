<?php
namespace PhalconRest\Entities;

class ChargeEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * auto detect a charge->name if a fee is provided and no other name is available
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::beforeSave()
     */
    function beforeSave($object, $id)
    {
        // auto populate a name if fee is specified
        if ($object->fee_id > 0 and strlen($object->name) == 0) {
            $fee = \PhalconRest\Models\Fees::findFirst($object->fee_id);
            $object->name = $fee->name;
        }
        // auto populate amount if a fee is specified
        if ($object->fee_id > 0 and $object->amount <= 0) {
            $fee = \PhalconRest\Models\Fees::findFirst($object->fee_id);
            $object->amount = $fee->amount;
        }
        // auto connect user_id if a registration is provided
        if ($object->registration_id > 0 and $object->user_id <= 0) {
            $registration = \PhalconRest\Models\Registrations::findFirst($object->registration_id);
            $object->user_id = $registration->attendee_id;
        }
        return $object;
    }
}