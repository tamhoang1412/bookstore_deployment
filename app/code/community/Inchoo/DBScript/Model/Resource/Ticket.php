<?php
 
class Inchoo_DBScript_Model_Resource_Ticket extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('inchoo_dbscript/ticket', 'ticket_id');
    }
}