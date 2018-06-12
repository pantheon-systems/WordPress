<?php if (!defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }
/**
 * Class WSAL_NP_NotificationBuilder
 * Utility class to manage a Notification
 * @author wp.kytten
 */
class WSAL_NP_NotificationBuilder
{
    protected $_deleteButtonText = '';
    protected $_saveButtonText = '';
    protected $_addButtonText = '';
    protected $_emailLabel = '';
    protected $_notifObj = null;

    function __construct()
    {
        $this->_deleteButtonText = __('Delete', 'email-notifications-wsal');
        $this->_saveButtonText = __('Save Notification', 'email-notifications-wsal');
        $this->_addeButtonText = __('Add Notification', 'email-notifications-wsal');
        $this->_emailLabel = __('Email address:', 'email-notifications-wsal');
        $this->_notifObj = new stdClass();
    }

    function GetSelect1Data()
    {
        return array('AND', 'OR');
    }
    function GetSelect2Data()
    {
        return array("ALERT CODE", "DATE", "TIME", "USERNAME", "USER ROLE", "SOURCE IP", "POST ID", "PAGE ID", "CUSTOM POST ID", "SITE DOMAIN", "POST TYPE");
    }
    function GetSelect3Data()
    {
        return array("IS EQUAL", "CONTAINS", "IS AFTER", "IS BEFORE", "IS NOT");
    }

    /**
     * Create the default Notification object
     * @param object|null $errors
     * @param object|null $info
     * @param object|null $buttons
     * @param object|null $default
     * @param array|null $triggers
     * @param array|null $viewState
     * @return null|stdClass
     */
    function create($errors=null, $info=null, $buttons=null, $default=null, $triggers=null, $viewState=null)
    {
        if ($errors) {
            $this->_notifObj->errors = $errors;
        } else {
            $this->_notifObj->errors = $this->createErrorsEntry();
        }

        if ($info) {
            $this->_notifObj->info = $info;
        } else {
            $this->_notifObj->info = $this->createInfoEntry();
        }

        if ($buttons) {
            $this->_notifObj->buttons = $buttons;
        } else {
            $this->_notifObj->buttons = $this->createButtonsEntry();
        }

        if ($default) {
            $this->_notifObj->default = $default;
        } else {
            $this->_notifObj->default = $this->createDefaultTrigger();
        }

        if ($triggers) {
            $this->_notifObj->triggers = $triggers;
        } else {
            $this->_notifObj->triggers = $this->createTriggersEntry();
        }

        if ($viewState) {
            $this->_notifObj->viewState = $viewState;
        } else {
            $this->_notifObj->viewState = $this->createViewStateEntry();
        }

        return $this->_notifObj;
    }

    /**
     * Update the $_notifObj with the provided values. It will add the key if not found and if the section is object,
     * will add the section if not found, as an object and add key and value to it
     * @param string $section
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    function update($section, $key, $value)
    {
        if (isset($this->_notifObj->$section)) {
            if (isset($this->_notifObj->$section->$key)) {
                $this->_notifObj->$section->$key = $value;
                return $this;
            } else {
                if (is_object($this->_notifObj->$section)) {
                    $this->_notifObj->$section->$key = $value;
                    return $this;
                }
            }
        }
        $this->_notifObj->$section = new stdClass();
        $this->_notifObj->$section->$key = $value;
        return $this;
    }

    /**
     * Update an entry in the errors.triggers object
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    function updateTriggerError($key, $value)
    {
        $this->_notifObj->errors->triggers->$key = $value;
        return $this;
    }

    /**
     * Update the viewState entry in the notifObj
     * @param array $data
     * @return $this
     */
    function UpdateViewState(array $data = array())
    {
        $this->_notifObj->viewState = $data;
        return $this;
    }

    /**
     * Add an entry into notifObj->triggers[]
     * @param object $triggerEntry
     * @return $this
     */
    function addTrigger($triggerEntry)
    {
        if (is_object($triggerEntry)) {
            array_push($this->_notifObj->triggers, $triggerEntry);
        }
        return $this;
    }

    /**
     * Clear the internal notifObj
     * @return $this
     */
    function clear()
    {
        $this->_notifObj = new stdClass();
        return $this;
    }

    /**
     * Reset trigger errors
     * @return $this
     */
    function clearTriggersErrors()
    {
        $this->_notifObj->errors = $this->createErrorsEntry();
        return $this;
    }

    /**
     * Retrieve the value of the specified key from the $_notifObj object
     *
     * DO NOT USE TO GET TRIGGERS! Use $this->getTriggers() instead
     *
     * @param $section
     * @param $key
     * @param bool $default If true, then key is searched in the default trigger entry
     * @return mixed or null if the key is not found
     */
    function getFrom($section, $key, $default = false)
    {
        if ($default) {
            if (isset($this->_notifObj->default->$section->$key)) {
                return $this->_notifObj->default->$section->$key;
            }
            return null;
        }
        if (isset($this->_notifObj->$section->$key)) {
            return $this->_notifObj->$section->$key;
        }
        return null;
    }

    /**
     * Retrieve all entries from notifObj->triggers
     * @return array
     */
    function getTriggers()
    {
        return $this->_notifObj->triggers;
    }

    /**
     * Retrieve the provided section from the $_notifObj object
     * @param string $section The section to retrieve from the $_notifObj object
     * @return array or null if the section is not found
     */
    function getSection($section)
    {
        if (isset($this->_notifObj->$section)) {
            return $this->_notifObj->$section;
        }
        return null;
    }

    /**
     * Retrieve the internal notifObj
     * @return null|stdClass
     */
    function get()
    {
        return $this->_notifObj;
    }

    /**
     * JSON Encode the provided object, if provided, or the internal notifObj
     * @param object|null $obj
     * @return mixed|string|void
     */
    function encodeForJs($obj = null)
    {
        if ($obj) {
            return json_encode($obj);
        }
        return json_encode($this->_notifObj);
    }

    /**
     * JSON Decode the provided string
     * @param string $objString
     * @return array|mixed|null
     */
    function decodeFromString($objString)
    {
        if (empty($objString)) {
            return null;
        }
        $objString  = str_replace('\\', '', $objString);
        return $this->_notifObj = json_decode(trim($objString));
    }

    /**
     * Create the default errors entry in the notifObj
     * @return stdClass
     */
    function createErrorsEntry()
    {
        $obj = new stdClass();
        $obj->titleMissing = '';
        $obj->titleInvalid = '';
        $obj->emailMissing = '';
        $obj->emailInvalid = '';
        $obj->triggersMissing = '';
        $obj->triggers = new stdClass();
        return $obj;
    }

    /**
     * Create the default buttons entry in the notifObj
     * @return stdClass
     */
    function createButtonsEntry()
    {
        $obj = new stdClass();
        $obj->deleteButton = $this->_deleteButtonText;
        $obj->saveNotifButton = $this->_saveButtonText;
        $obj->addNotifButton = $this->_addButtonText;
        return $obj;
    }

    /**
     * Create the default triggers entry in the notifObj
     * @return array
     */
    function createTriggersEntry()
    {
        return array();
    }

    /**
     * Create the default groups entry in the notifObj
     * @return array
     */
    function createViewStateEntry()
    {
        return array();
    }

    /**
     * Create the default info entry in the notifObj
     * @return stdClass
     */
    function createInfoEntry()
    {
        $obj = new stdClass();
        $obj->title = '';
        $obj->email = '';
        $obj->emailLabel = $this->_emailLabel;
        return $obj;
    }

    /**
     * Create the default trigger entry in the notifObj
     * @return stdClass
     */
    function createDefaultTrigger()
    {
        $obj = new stdClass();
        $obj->select1 = new stdClass();
        $obj->select1->data = $this->GetSelect1Data();
        $obj->select1->selected = 0;

        $obj->select2 = new stdClass();
        $obj->select2->data = $this->GetSelect2Data();
        $obj->select2->selected = 0;

        $obj->select3 = new stdClass();
        $obj->select3->data = $this->GetSelect3Data();
        $obj->select3->selected = 0;

        $obj->input1 = '';
        $obj->deleteButton = $this->_deleteButtonText;
        return $obj;
    }
}
