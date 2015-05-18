<?php
namespace PhalconRest\Models;

class Cards extends \PhalconRest\API\BaseModel
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
    public $account_id;

    /**
     *
     * @var string
     */
    public $external_id;

    /**
     *
     * @var string
     */
    public $created_on;

    /**
     *
     * @var integer
     */
    public $allow_reoccuring;

    /**
     *
     * @var string
     */
    public $expiration_month;

    /**
     *
     * @var string
     */
    public $expiration_year;

    /**
     *
     * @var string
     */
    public $name_on_card;

    /**
     *
     * @var string
     */
    public $number;

    /**
     *
     * @var string
     */
    public $vendor;

    /**
     *
     * @var integer
     */
    public $is_debit;

    /**
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\BaseModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->belongsTo("account_id", "PhalconRest\Models\Accounts", "id", array(
            'alias' => 'Accounts'
        ));
        
        $this->hasMany("id", "PhalconRest\Models\Payments", "card_id", array(
            'alias' => 'Payments'
        ));
    }
}
