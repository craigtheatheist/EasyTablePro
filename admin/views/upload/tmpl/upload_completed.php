<?php
/**
 * @package    EasyTables
 * @author     Craig Phillips {@link http://www.seepeoplesoftware.com}
 * @author     Created on 13-Jul-2009
 */

//--No direct access
	defined('_JEXEC') or die('Restricted Access');
?>

<table class="adminlist" id="et_uploadCompleted">
	<tr class="row0">
		<td width="120" align="right" valign="top" class="key">
		<span style="font-size: 1.5em;font-weight: bold;"><label><?php echo JText::_( 'COM_EASYTABLEPRO_UPLOAD_RESULTS' ); ?>:</label></span>
		</td>
		<td><?php
			echo html_entity_decode(JText::sprintf( ('COM_EASYTABLEPRO_UPLOAD_NOTES_' . $this->status), $this->dataFile, $this->form->getValue('easytablename') ) . ' ');
			echo JText::sprintf('COM_EASYTABLEPRO_UPLOAD_NOTES_RECORD_COUNT', $this->prevAction, $this->form->getValue('easytablename'), $this->uploadedRecords);
			?></td>
	</tr>
</table>