/**
 * @package    EasyTable_Pro
 * @author     Craig Phillips <craig@craigphillips.biz>
 * @copyright  Copyright (C) 2012-2014 Craig Phillips Pty Ltd.
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @url        http://www.seepeoplesoftware.com
 */

Joomla.submitbutton = function (pressbutton)
{
    "use strict";

	if (pressbutton === 'uploadFile')
	{
		var tFileName = document.adminForm.tablefile.value;
		if (tFileName === '')
		{
			alert(Joomla.JText._('COM_EASYTABLEPRO_DATA_JS_PLEASE_CHOOSE_A_FILE'));
			return 0;
		}
		
		var dot = tFileName.lastIndexOf(".");
		if (dot === -1)
		{
			alert (Joomla.JText._('COM_EASYTABLEPRO_UPLOAD_JS_ONLY_FILES_WITH_A_CSV_OR_TAB_EXTENSION_ARE_SUPPORTED_NO_EXTENSION_FOUND'));
			return 0;
		}
		
		var tFileExt = tFileName.substr(dot,tFileName.length);
		tFileExt = tFileExt.toLowerCase();

		if ((tFileExt !== ".csv") && (tFileExt !== ".tsv"))
		{
			alert (com_EasyTablePro.Tools.sprintf(Joomla.JText._('COM_EASYTABLEPRO_UPLOAD_JS_ONLY_FILES_WITH_AN_EXTENSION_OF_CSV_OR_TAB_ARE_SUPPORTED_FOUND_X'), tFileExt));
			return 0;
		}
		else
		{
			submitform(pressbutton);
		}
	}
	else 
	{
		alert(com_EasyTablePro.Tools.sprintf(Joomla.JText._('COM_EASYTABLEPRO_TABLE_JS_WARNING_OK_YOU_BROKE_IT'), pressbutton));
		return 0;
	}
}
