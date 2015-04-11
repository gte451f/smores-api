<?php
namespace PhalconRest\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\Numericality as NumericalityValidator;

class Programs extends \PhalconRest\API\BaseModel
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
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var double
     */
    public $cost;
    
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

        $this->hasMany("id", "PhalconRest\Models\Events", "program_id", array(
            'alias' => 'Events'
        ));
    }

    public function validation()
    {
        $this->validate(new NumericalityValidator(array(
            'field' => 'cost'
        )));
        
        $this->validate(new Uniqueness(array(
            "field" => "name",
            "message" => "The program name must be unique"
        )));
        
        // $result = $this->validationHasFailed();
        
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
