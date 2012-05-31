<?php /**
 * @package     EasyTable Pro
 * @Copyright   Copyright (C) 2012 Craig Phillips Pty Ltd.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @author      Craig Phillips {@link http://www.seepeoplesoftware.com}
 */
 

//--No direct access
defined('_JEXEC') or die ('Restricted Access');

jimport( 'joomla.application.component.modelitem' );

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/general.php';

/**
 * EasyTableProRecord Model
 *
 * @package	   EasyTablePro
 * @subpackage Models
 */
class EasyTableProModelRecord extends JModelItem
{
	protected $_context = 'com_easytablepro.record';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('table.id', $pk);

		$pk = JRequest::getInt('rid');
		$this->setState('record.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	public function &getItem($pk = null)
	{
		// Initialise variables.
		$etID = (!empty($pk)) ? $pk : (int) $this->getState('table.id');
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('record.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$etID.'.'.$pk])) {

			try {
				// Get our DB connection
				$db = $this->getDbo();
				// Setup a new query
				$query = $db->getQuery(true);
				// Get our table meta data
				$et = ET_Helper::getEasytableMetaItem($etID);
				if(!$et) {
					return JError::raiseError(404, JText::_('COM_EASYTABLEPRO_RECORD_ERROR_TABLE_NOT_FOUND'));
				}
				// @todo move to general helper functions
				// First up lets convert these params to a JRegister
				$rawParams = $et->params;
				$paramsObj = new JRegistry();
				$paramsObj->loadArray($rawParams);
				$et->params = $paramsObj;

				// Get our record from the right table
				$query->select('*');
				$query->from($db->quoteName($et->ettd_tname));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
				$db->setQuery($query);

				// Get our elements for next & prev records
				$menuParams = $this->getState('params', null);
				$orderFieldId = $menuParams->get('sort_field', 0);
				if($orderFieldId != 0) {
					$orderField = $et->table_meta[$orderFieldId]['fieldalias'];
					$ordDir = $menuParams->get('sort_order', 'ASC');
				} else {
					$orderField = 'id';
					$ordDir = 'ASC';
				}
				$title_field = $et->params->get('title_field');
				$title_field = $et->table_meta[$title_field]['fieldalias'];
				$record = $db->loadObject();
				// @todo add title_field id to prev/next request to retreive leaf 
				$prevId = $this->getAdjacentId($et->ettd_tname, $orderField, $ordDir, $record->$orderField, $title_field);
				$nextId = $this->getAdjacentId($et->ettd_tname, $orderField, $ordDir, $record->$orderField, $title_field, true);
				
				// Do we need linked records?
				$show_linked_table = $et->params->get('show_linked_table',0);
				$linked_table = $linked_data = $let = null;

				if($show_linked_table) {
					$linked_table   = $et->params->get('id', 0);
					$key_field   = $et->params->get('key_field', 0);
					$linked_key_field = $et->params->get('linked_key_field', 0);
					// We need all 3 id's to proceed
					if($linked_table && $key_field && $linked_key_field) {
						// Retreive the linked table
						$let = ET_Helper::getEasytableMetaItem($linked_table);
						$letP = new JRegistry();
						$letP->loadArray($let->params);
						$let->params = $letP;
						$key_field = $et->table_meta[$key_field]['fieldalias'];
						$key_field_value = $record->$key_field;
						$linked_key_field_meta = $let->table_meta[$linked_key_field];
						$linked_key_field = $linked_key_field_meta['fieldalias'];
						$linked_data = $this->getLinked($let,$key_field_value,$linked_key_field);
						if(count($linked_data)) {
							$et->params->set('show_linked_table', false);
							$linked_table = $linked_data = $let = null;
						}
					} else {
						$et->params->set('show_linked_table', false);
					}
				}

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($record)) {
					return JError::raiseError(404, JText::_('COM_EASYTABLEPRO_RECORD_ERROR_RECORD_NOT_FOUND'));
				}

				// Compute selected asset permissions.
				$user	= JFactory::getUser();

				// Compute view access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$et->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					$et->params->set('access-view', in_array($et->access, $groups));
				}
				$item = (object) array('easytable' => $et, 'record' => $record, 'prevRecordId' => $prevId, 'nextRecordId' => $nextId, 'linked_table' => $let, 'linked_records' => $linked_data);

				$this->_item[$etID . '.' . $pk] = $item;
			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$etID . '.' . $pk] = false;
				}
			}
		}

		return $this->_item[$etID . '.' . $pk];
	}

	protected function getLinked ($linked_table = null, $key_field_value = '', $linked_key_field = '')
	{
		if(($linked_table == null) || ($key_field_value == '') || ($linked_key_field == ''))
		{
			return false;
		} else {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			// Get all fields of our matching records
			$query->select('*');
			$query->from($db->quoteName($linked_table->ettd_tname));
			$query->where($db->quoteName($linked_key_field) . ' = ' . $db->quote($key_field_value));
			// Set our query and retreive our records
			$db->setQuery($query);
			$linked_data = $db->loadAssocList();
			return $linked_data;
		}
	}

	protected function getAdjacentId ($tableName='', $orderField, $ordDir, $currentOrderFieldValue, $leafField, $next=FALSE)
	{
		// Do we need to flip for reverse sort order
		if($ordDir == 'DESC') $next = !$next;
		// Next record?
		if($next)
		{
			$eqSym = '>';
			$sortOrder =  'ASC';
		}
		else
		{ // So prev. record.
			$eqSym = '<';
			$sortOrder =  'DESC';
		}

		// Get the current database object
		$db = JFactory::getDBO();
		if(!$db){
			JError::raiseError(500,JText::_( "COM_EASYTABLEPRO_SITE_DB_NOT_AVAILABLE_CREATING_NEXTPREV_RECORD_LINK" ).$mId );
		}
		// New query
		$query = $db->getQuery(true);

		$query->from($db->quoteName( $tableName ));
		$query->select($db->quoteName('id'));
		$query->select($db->quoteName($leafField));
		$query->where($db->quoteName($orderField) . ' ' . $eqSym . ' ' . $currentOrderFieldValue);
		$query->order($db->quoteName($orderField) . ' ' . $sortOrder);
		$db->setQuery($query, 0, 1);

		$adjacentRow = $db->loadRow();
		// Convert leaf to URL safe
		$adjacentRow[1] = JFilterOutput::stringURLSafe(substr($adjacentRow[1], 0,100));
		return $adjacentRow;
	}
}