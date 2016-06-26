<?php
namespace PhalconRest\Models;

/**
 * backed by an ever changing view
 * see Fields controller for more
 *
 * @author jjenkins
 *
 */
class CustomRegistrationFields extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $registration_id;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();

        $this->belongsTo("registration_id", "PhalconRest\\Models\\Registrations", "id", array(
            'alias' => 'Registrations'
        ));
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::getPrimaryKeyName()
     */
    public function getPrimaryKeyName()
    {
        return 'registration_id';
    }

    /**
     * hide user_id in favor of parent id
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns($withParents = true)
    {
        $this->setBlockColumns([
            'registration_id'
        ]);
    }
}
