<?php
namespace PhalconRest\Entities;

class AttendeeEntity extends \PhalconRest\API\Entity
{
    final public function __construct($model, $searchHelper)
    {
        // apply before construct
        $this->parentModel = 'Users';
        parent::__construct($model, $searchHelper);
    }
}