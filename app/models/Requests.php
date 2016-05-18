<?php
namespace PhalconRest\Models;

class Requests extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $registration_id;

    /**
     *
     * @var integer
     */
    public $event_id;

    /**
     *
     * @var integer
     */
    public $priority;

    /**
     * should this request count against capacity?
     *
     * @var integer
     */
    public $attending;

    /**
     *
     * @var string
     */
    public $submit_status;

    /**
     *
     * @var string
     */
    public $note;

    /**
     * define custom model relationships
     *
     * (non-PHPdoc)
     *
     * @see extends \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();

        $this->belongsTo('registration_id', 'PhalconRest\Models\Registrations', 'id', array(
            'alias' => 'Registrations'
        ));

        $this->belongsTo('event_id', 'PhalconRest\Models\Events', 'id', array(
            'alias' => 'Events'
        ));
    }

    public function beforeValidationOnCreate()
    {
        // auto populate the attending boolean
        // will probably push this to a full auto detecting function
        $this->attending = 0;
        $this->submit_status = 'New';
    }

    /**
     * dynamic toggle fields based on who is asking
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\BaseModel::loadBlockColumns()
     */
    public function loadBlockColumns()
    {
        $blockColumns = [];
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();

        if ($currentUser->userType != 'Employee') {
            $blockColumns[] = 'attending';
        }
        $this->setBlockColumns($blockColumns, true);
    }
}
