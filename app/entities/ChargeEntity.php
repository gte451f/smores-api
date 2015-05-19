<?php
namespace PhalconRest\Entities;

class ChargeEntity extends \PhalconRest\API\Entity
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
        if ($object->fee_id > 0 and strlen($object->name) == 0) {
            $fee = \PhalconRest\Models\Fees::findFirst($object->fee_id);
            $object->name = $fee->name;
        }
        
        return $object;
    }
}