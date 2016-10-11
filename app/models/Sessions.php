<?php
namespace PhalconRest\Models;

class Sessions extends \PhalconRest\API\BaseModel
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

        $this->hasMany("id", "PhalconRest\\Models\\Events", "program_id", array(
            'alias' => 'Events'
        ));
    }
}
