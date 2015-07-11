<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionInValidator;

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
        $this->hasOne("user_id", "PhalconRest\Models\Users", "id", array(
            'alias' => 'Users'
        ));
        $this->belongsTo('account_id', 'PhalconRest\Models\Accounts', 'id', array(
            'alias' => 'Accounts'
        ));
        
        $this->hasMany("user_id", "PhalconRest\Models\OwnerNumbers", "owner_id", array(
            'alias' => 'OwnerNumbers'
        ));
    }


    /**
     * validation owern data
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
