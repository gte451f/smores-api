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
    public $programs_id;

    /**
     *
     * @var integer
     */
    public $locations_id;

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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'programs_id' => 'programs_id', 
            'locations_id' => 'locations_id', 
            'start' => 'start', 
            'end' => 'end', 
            'min_age' => 'min_age', 
            'max_age' => 'max_age', 
            'gender' => 'gender', 
            'capacity' => 'capacity'
        );
    }

}
