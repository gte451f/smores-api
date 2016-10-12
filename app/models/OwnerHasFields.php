<?php
namespace PhalconRest\Models;

use PhalconRest\API\BaseModel;

class OwnerHasFields extends BaseModel
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
     * @var integer
     */
    public $field_id;

    /**
     *
     * @var string
     */
    public $value;

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
        $this->belongsTo("user_id", Users::class, "id", ['alias' => 'Users']);
        $this->belongsTo('owner_id', Owners::class, 'id', ['alias' => 'Owners']);
        $this->belongsTo('field_id', Fields::class, 'id', ['alias' => 'Fields']);
    }
}
