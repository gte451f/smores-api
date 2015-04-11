<?php
namespace PhalconRest\Models;

class Events extends \PhalconRest\API\BaseModel
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
    public $program_id;

    /**
     *
     * @var integer
     */
    public $location_id;

    /**
     *
     * @var string
     */
    public $start;

    /**
     *
     * @var string
     */
    public $end;

    /**
     *
     * @var integer
     */
    public $min_age;

    /**
     *
     * @var integer
     */
    public $max_age;

    /**
     *
     * @var string
     */
    public $gender;

    /**
     *
     * @var integer
     */
    public $capacity;

    /**
     *
     * @var double
     */
    public $cost;
    
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
    
        $this->belongsTo('location_id', 'PhalconRest\Models\Locations', 'id', array(
            'alias' => 'Locations'
        ));
        
        $this->belongsTo('program_id', 'PhalconRest\Models\Programs', 'id', array(
            'alias' => 'Programs'
        ));
    }
}
