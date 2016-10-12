<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class Sessions extends BaseModel
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
