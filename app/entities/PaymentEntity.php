<?php
namespace PhalconRest\Entities;

use \PhalconRest\Util\ValidationException;

class PaymentEntity extends \PhalconRest\API\Entity
{

    /**
     * remove the check record if one was connected to it
     * no need to remove card_id since the card might be used on multiple payments
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterDelete()
     */
    public function afterDelete($model)
    {
        // extend me in child class
        if ($model->check_id > 0) {
            $check = \PhalconRest\Models\Checks::findFirst($model->check_id);
            if ($check->delete() == false) {
                throw new ValidationException("Internal error removing check record.  This error has been logged.", array(
                    'internalCode' => '29629674',
                    'dev' => 'Error while attempting to delete a check after the related payment was removed.'
                ), $check->getMessages());
            }
        }
    }
}