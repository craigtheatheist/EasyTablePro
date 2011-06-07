<?php
/**
 * @package	   EasyTables
 * @author	   Craig Phillips {@link http://www.seepeoplesoftware.com}
 * @author	   Created on 13-Jul-2009
 */

//--No direct access
defined('_JEXEC') or die('Restricted Access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the EasyTables Component
 *
 * @package	   EasyTables
 * @subpackage Views
 */

class EasyTableVieweasytablelink extends JView
{
	/**
	 * View display method
	 * 
	 * @return void
	 **/
	function convertValueArrToKVObjArr ($arr)
	{
		$retArr = array ();
		foreach ( $arr as $item )
		{
			$retArr[] = array('value' => $item, 'text' => $item);
		}

		return $retArr;
	}

	function getAlreadyLinkedTables()
	{
		//get the list of tables
		$db =& JFactory::getDBO();
		if(!$db){
			JError::raiseError(500,JText::_("UNABLE_TO_DESC"));
		}
		$query = "SELECT `datatablename` FROM `#__easytables` WHERE `datatablename` > ''";
		$db->setQuery($query);
		$alreadyLinkedTables = $db->loadResultArray();
		return $alreadyLinkedTables;
	}

	function stripRestrictedTables ($arr)
	{
		// Setup defaults
		$arrOfRestrictedTables = array("easytables","_core_acl","_session");
		// and are merged with the user set restrictions
		$settings = ET_MgrHelpers::getSettings();
		if(trim($settings->get('restrictedTables')) == '')
		{
			$userRestrictedTable = array();
		}
		else
		{
			$userRestrictedTable = explode ( "\n", trim($settings->get('restrictedTables')) );
		}
		// Get & Merge the user restricted tables into our array here.
		$arrOfRestrictedTables = array_unique(array_merge( $userRestrictedTable, $arrOfRestrictedTables ));
		// Get & Merge the already linked tables into our array here.
		$arrOfTablesAlreadyLinked = $this->getAlreadyLinkedTables();
		$arrOfRestrictedTables = array_merge ( $arrOfRestrictedTables, $arrOfTablesAlreadyLinked );
		// Loop through and clean out restricted tables from selection list
		foreach ( $arrOfRestrictedTables as $restrictedElement )
		{
			foreach ( $arr as $key=>$tableNameArray )
			{
				$tableName = $tableNameArray['value'];
				$tableInRestrictedList = strpos($tableName,$restrictedElement); // don't forget strpos returns FALSE
				if(($tableInRestrictedList === 0) || ($tableInRestrictedList))// if the tableName is restricted
				{
					unset ( $arr[$key] ); // then remove it from the list
				}
			}
			reset ( $arrOfRestrictedTables );
		}
	}

	function display($tpl = null)
	{
		//get the document and load the js support file
		$doc =& JFactory::getDocument();
		$u = & JURI::getInstance();
		$doc->addStyleSheet('/templates/system/css/system.css');
		$doc->addScript(JURI::base().'components/com_'._cppl_this_com_name.'/assets/js/easytablelink.js');


		//get the list of tables
		$db =& JFactory::getDBO();
		if(!$db){
			JError::raiseError(500,JText::_("UNABLE_TO_DESC"));
		}
		// Get the meta data for this table
		$allTables = $db->getTableList();

		// swap up the array to needed format
		$allTables = $this->convertValueArrToKVObjArr($allTables);
		// strip out tables in the restricted list
		$this->stripRestrictedTables (& $allTables);
		if(count($allTables))
		{
			// prefix with a 'None Selected' option
			$noneSelected = array();
			$noneSelected[] = array('value' => 0,'text' => '-- '.JText::_( "None Selected" ).' --');
			array_splice($allTables,0,0,$noneSelected);
			$tablesAvailableForSelection = TRUE;
		}
		else
		{	// dang an empty list of tables.
			$noneAvailable = array();
			$noneAvailable[] = array('value' => 0,'text' => '-- '.JText::_( "None Available" ).' --');
			array_splice($allTables,0,0,$noneAvailable);
			$tablesAvailableForSelection = FALSE;
		}

		// covert to a HTML select otpion
		$tableList = JHTML::_('select.genericlist',  $allTables, 'tablesForLinking');
		// Parameters for this table instance
		$this->assignRef('tableList',$tableList);
		$this->assign('tablesAvailableForSelection',$tablesAvailableForSelection);
		parent::display($tpl);
	}
}
