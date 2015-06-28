<?php
namespace PhalconRest\Entities;

class EventEntity extends \PhalconRest\API\Entity
{

    /**
     * always return event relations, who just wants an event table anyway?
     * see registrations/info
     */
    public function configureSearchHelper()
    {
        $this->searchHelper->entityWith = 'all';
    }
}