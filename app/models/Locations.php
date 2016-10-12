<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class Locations extends BaseModel
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
     * define custom model relationships
     *
     * (non-PHPdoc)
     *
     * @see extends \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->hasMany("id", Events::class, "program_id", ['alias' => 'Events']);
    }
}
