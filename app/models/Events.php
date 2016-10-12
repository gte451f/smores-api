<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class Events extends BaseModel
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
    public $name;

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
    public $fee;

    /**
     *
     * @var string
     */
    public $fee_description;

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
        $this->belongsTo('location_id', Locations::class, 'id', ['alias' => 'Locations']);
        $this->belongsTo('program_id', Programs::class, 'id', ['alias' => 'Programs']);
        $this->belongsTo('cabin_id', Cabins::class, 'id', ['alias' => 'Cabins']);
        $this->belongsTo('session_id', Sessions::class, 'id', ['alias' => 'Sessions']);
    }
}
