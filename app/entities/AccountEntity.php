<?php
namespace PhalconRest\Entities;

class AccountEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * always include custom fields, regardless of what the client asks for
     */
    public function configureSearchHelper()
    {
        $this->searchHelper->entityWith = 'custom_account_fields';
    }

    /**
     * remove user record(s) from the account
     * this is due to the way FKs are established on attendee/owner records
     * they in turn do not cascade up to the user table
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::beforeDelete()
     */
    public function beforeDelete($model)
    {
        // clean out records via PHP
        $list = [
            'owners',
            'attendees'
        ];
        foreach ($list as $member) {
            $members = $model->$member;
            foreach ($members as $member) {
                $user = $member->users;
                if ($user) {
                    $user->delete();
                }
            }
        }
    }
}