<?php
namespace PhalconRest\Models;

/**
 * backed by an ever changing view
 * see Fields controller for more
 *
 * @author jjenkins
 *        
 */
class CustomAttendeeFields extends \PhalconRest\API\BaseModel
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
        
        $this->belongsTo("user_id", "PhalconRest\Models\Attendees", "id", array(
            'alias' => 'Attendees'
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
        return 'user_id';
    }

    /**
     * hide user_id in favor of parent id
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns()
    {
        $this->setBlockColumns([
            'user_id'
        ]);
    }
}
