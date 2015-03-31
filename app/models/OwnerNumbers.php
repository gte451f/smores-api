<?php
namespace PhalconRest\Models;

class OwnerNumbers extends \PhalconRest\API\BaseModel
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
    public $user_id;

    /**
     *
     * @var string
     */
    public $phone_type;

    /**
     *
     * @var integer
     */
    public $primary;

    /**
     *
     * @var string
     */
    public $number;
}
