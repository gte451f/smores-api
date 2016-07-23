<?php
namespace PhalconRest\Entities;

class OwnerNumberEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * custom implementation of account filter
     * must filter through related owner records
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\API\Entity::applyAccountFilter()
     */
    public function applyAccountFilter($query)
    {
        // load current account
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        // add custom filter
        // $query->andWhere("PhalconRest\\Models\\Owners.account_id = $currentUser->accountId");
        $randomName = 'rand' . rand(1, 1000000);
        $query->andWhere("PhalconRest\\Models\\Owners.account_id = :$randomName:", [
            $randomName => $currentUser->accountId
        ]);


        // only add needed join if it isn't already in place
        // 1 = hasOne 0 = belongsTo 2 = hasMany
        $applyJoin = true;
        foreach ($this->activeRelations as $alias => $relation) {
            if ($alias == 'Owners' and $relation->getType() == 1) {
                $applyJoin = false;
                break;
            }
        }
        if ($applyJoin) {
            $query->join("PhalconRest\\Models\\Owners");
        }
        return $query;
    }
}