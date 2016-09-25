<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Owners extends \PhalconRest\API\BaseModel
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     * 0 | 1
     *
     * @var integer
     */
    public $primary_contact;

    /**
     * Mother|Father|Grand Parent|Other|Guardian
     *
     * @var string
     */
    public $relationship;

    /**
     * this model's parent model
     *
     * @var string
     */
    public static $parentModel = 'Users';

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
        $this->hasOne("user_id", Users::class, "id", ['alias' => 'Users']);
        $this->belongsTo('account_id', Accounts::class, 'id', ['alias' => 'Accounts']);
        $this->hasMany("user_id", OwnerNumbers::class, "owner_id", ['alias' => 'OwnerNumbers']);
        $this->hasOne('user_id', CustomOwnerFields::class, 'user_id', ['alias' => 'CustomOwnerFields']);
    }

    /**
     * validation owner data
     */
    public function validation()
    {
        $this->validate(new InclusionInValidator(array(
            'field' => 'relationship',
            'domain' => array(
                'Mother',
                'Father',
                'Guardian',
                'Grand Parent',
                'Other'
            )
        )));

        return $this->validationHasFailed() != true;
    }
}
