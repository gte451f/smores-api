<?php
namespace PhalconRest\Models;

class Locations extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $addr_1;

    /**
     *
     * @var string
     */
    public $addr_2;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $state;

    /**
     *
     * @var string
     */
    public $country;

    /**
     *
     * @var string
     */
    public $zip;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'addr_1' => 'addr_1', 
            'addr_2' => 'addr_2', 
            'city' => 'city', 
            'state' => 'state', 
            'country' => 'country', 
            'zip' => 'zip', 
            'name' => 'name', 
            'description' => 'description'
        );
    }

}
