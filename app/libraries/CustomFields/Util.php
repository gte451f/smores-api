<?php
namespace PhalconRest\Libraries\CustomFields;

use \PhalconRest\Util\HTTPException;
use Phalcon\DI\Injectable;
use \PhalconRest\Models\PhalconRest\Models;
use \PhalconRest\Libraries\File\Util as FileUtil;

/**
 * TBWritten
 */
class Util extends Injectable
{

    private $di = null;

    /**
     * init
     *
     * @param unknown $key
     */
    function __construct()
    {
        // $di = \Phalcon\DI::getDefault();
        // $this->di = $di;
    }

    /**
     * destroy and reubild the SQL view
     *
     * supports:
     * attendees, accounts, owners, registrations
     *
     * @param string $view
     */
    public static function rebuildView($view)
    {
        switch ($view) {
            case 'registrations':
                $singular = 'registration';
                $foreignKey = 'registration_id';
                break;

            case 'accounts':
                $singular = 'account';
                $foreignKey = 'account_id';
                break;

            case 'owners':
                $singular = 'owner';
                $foreignKey = 'user_id';
                break;
            case 'attendees':
                $singular = 'attendee';
                $foreignKey = 'user_id';
                break;
            default:
                // uh oh, unrecognized view supplied
                throw new HTTPException("Invalid view specified.", 404, array(
                    'dev' => 'Supplied view:  ' . $view . ' did not match any known values.',
                    'code' => '6468464646466464'
                ));
                break;
        }

        // gather a list of fields for the given table
        $fieldString = '';
        $fieldList = [];
        $fields = \PhalconRest\Models\Fields::find("table = '$view'");
        foreach ($fields as $key => $value) {
            $fieldList[] = "MAX(CASE WHEN f.name = '$value->name' THEN VALUE END) AS $value->name" . PHP_EOL;
        }
        if (count($fieldList) > 0) {
            $fieldString = ',' . implode(", ", $fieldList);
        }

        // default view txt
        $sql = "CREATE OR REPLACE VIEW `custom_" . $singular . "_fields` AS
                ( SELECT 
                    thf.$foreignKey
                    $fieldString
                    FROM
                    " . $singular . "_has_fields AS thf
                    JOIN
                    `fields` AS f ON f.id = thf.field_id
                    GROUP BY thf.$foreignKey );";

        $di = \Phalcon\DI::getDefault();
        $db = $di->get('db');
        $result = $db->query($sql);

        // now wipe cache so future request to api will pull latest fields
        $file = new FileUtil();
        $file->clearCache();
    }

    /**
     * for a given list of fields (with data) and the table they are related to
     * save each individual field into the correct *_has_fields table
     *
     * @param object $object
     * @param string $table
     * @param int $id
     *            the pkid of the parent record
     */
    function saveFields($object, $table, $id)
    {
        $customFields = \PhalconRest\Models\Fields::find("table = '$table'");

        $metaValues = $this->detectModel($table);
        $tableModel = $metaValues['model'];
        $foreignKey = $metaValues['key'];

        foreach ($customFields as $customField) {
            // see if any data is present for each field
            $customFieldName = $customField->name;
            if (isset($object->$customFieldName)) {
                $newField = $tableModel::findFirst("field_id=$customField->id AND $foreignKey=$id");
                if (!$newField) {
                    $newField = new $tableModel();
                    $newField->field_id = $customField->id;
                    $newField->$foreignKey = $id;
                }
                $newField->value = $object->$customFieldName;

                // persist to database
                if ($newField->save() == false) {
                    throw new ValidationException("Could not save custom value", array(
                        'dev' => "Error saving a custom field for table: $table and # $id",
                        'code' => '2865586241346'
                    ), $user->getMessages());
                }
            }
        }
    }

    /**
     * for a given table, return a string pointing to the correct *HadFields
     *
     * @param unknown $table
     * @throws HTTPException
     */
    private function detectModel($table)
    {
        // calc class namespace
        switch ($table) {
            case 'attendees':
                $tableModel = "\\PhalconRest\\Models\\AttendeeHasFields";
                $foreignKey = 'user_id';
                break;

            case 'registrations':
                $tableModel = "\\PhalconRest\\Models\\RegistrationHasFields";
                $foreignKey = 'registration_id';
                break;

            case 'accounts':
                $tableModel = "\\PhalconRest\\Models\\AccountHasFields";
                $foreignKey = 'account_id';
                break;

            case 'owners':
                $tableModel = "\\PhalconRest\\Models\\OwnerHasFields";
                $foreignKey = 'user_id';
                break;

            default:
                throw new HTTPException("Unknown table supplied", 400, array(
                    'dev' => "Supplied table was: $table",
                    'code' => '28349237927'
                ));
                break;
        }

        return [
            'model' => $tableModel,
            'key' => $foreignKey
        ];
    }
}