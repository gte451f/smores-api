<?php
namespace PhalconRest\Entities;

class UserEntity extends \PhalconRest\API\Entity
{
    // when a user is deleted, also remove from owner, attendee and employee records
    // transaction anyone?
}