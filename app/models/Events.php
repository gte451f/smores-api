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
}