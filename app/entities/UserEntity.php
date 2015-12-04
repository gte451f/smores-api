<?php
namespace PhalconRest\Entities;

class UserEntity extends \PhalconRest\Libraries\API\Entity
{
    // when a user is deleted, also remove from owner, attendee and employee records
    // transaction anyone?
}