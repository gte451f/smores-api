<?php
namespace PhalconRest\Models;

class OwnerHasFields extends \PhalconRest\API\BaseModel
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
        $this->belongsTo("user_id", "PhalconRest\\Models\\Users", "id", array(
            'alias' => 'Users'
        ));

        $this->belongsTo('owner_id', 'PhalconRest\Models\Owners', 'id', array(
            'alias' => 'Owners'
        ));

        $this->belongsTo('field_id', 'PhalconRest\Models\Fields', 'id', array(
            'alias' => 'Fields'
        ));
    }
}
