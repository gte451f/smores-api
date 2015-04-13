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
     * define custom model relationships
     *
     * (non-PHPdoc)
     *
     * @see extends \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        
        $this->belongsTo('registration_id', 'PhalconRest\Models\Registration', 'id', array(
            'alias' => 'Registrations'
        ));
        
        $this->belongsTo('event_id', 'PhalconRest\Models\Events', 'id', array(
            'alias' => 'Events'
        ));
    }
}
