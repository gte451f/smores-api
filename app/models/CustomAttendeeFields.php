<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

/**
 * backed by an ever changing view
 * see Fields controller for more
 *
 * @author jjenkins
 *
 */
class CustomAttendeeFields extends BaseModel
{

    /**
     *
     * @var integer
     */
    public $attendee_id;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("user_id", Attendees::class, "id", ['alias' => 'Attendees']);
    }

    /**
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::getPrimaryKeyName()
     */
    public function getPrimaryKeyName()
    {
        return 'user_id';
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
            'user_id'
        ]);
    }
}
