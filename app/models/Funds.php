<?php
namespace PhalconRest\Models;

class Funds extends \PhalconRest\API\BaseModel
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
    public $source;

    /**
     *
     * @var string
     */
    public $external_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $expires_month;

    /**
     *
     * @var integer
     */
    public $expires_year;


}
