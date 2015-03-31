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
}
