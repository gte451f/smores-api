<?php
namespace PhalconRest\Entities;

class OwnerEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * always include custom fields, regardless of what the client asks for
     */
    public function configureSearchHelper()
    {
        $this->searchHelper->entityWith = 'custom_owner_fields';
    }

    /**
     * deal with any custom fields that may have been submitted
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::afterSave()
     */
    public function afterSave($object, $id = null)
    {
        // process custom fields as part of general save
        // treat updates/adds the same
        $fieldService = new \PhalconRest\Libraries\CustomFields\Util();
        $fieldService->saveFields($object, 'owners', $id);
    }

    /**
     * auto assign user_type to form
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::beforeSave()
     */
    public function beforeSave($object, $id = null)
    {
        // extend me in child class
        $object->user_type = 'Owner';

        return $object;
    }

    /**
     * remove user record
     * really renders the subsequent delete worthless, but this is the cleanest way to avoid partial deletes
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\API\Entity::beforeDelete()
     * @param \PhalconRest\API\BaseModel $model
     */
    public function beforeDelete(\PhalconRest\API\BaseModel $model)
    {
        // extend me in child class
        $user = $model->users;
        if ($user) {
            $user->delete();
        }
    }
}