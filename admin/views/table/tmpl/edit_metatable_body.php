<?php
//--No direct access
defined('_JEXEC') or die('Restricted Access');
?>

<tbody id='et_meta_table_rows'>
<?php
	$mRIds = array();
	$k = 0;
	foreach ($this->item->table_meta as $metaRow)
	{
		$mRId    = $metaRow['id'];
		$mRIds[] = $mRId;
		$rowID   = 'et_rID'.$mRId;
		$tdEnd   = '</td>';

		// Open the row
		echo '<tr valign="top" class="row'.$k.'" id="'.$rowID.'">';

		echo('<td align="center"><input type="hidden" name="id'.$mRId.'" value="'.$mRId.'" />'.$mRId );if (!$this->item->etet){echo( '<br /><a href="javascript:void(0);" class="deleteFieldButton-nodisplay" onclick="com_EasyTablePro.Table.deleteField(\''.$metaRow['label'].'\', \''.$rowID.'\');"><img src="'.JURI::root().'media/com_easytablepro/images/publish_x.png" alt="Toggle Publish state." /></a>'); } echo ('</td>');				// Id
		echo('<td align="center"><input type="text" value="'.$metaRow['position'].'" size="3" name="position'.$mRId.'"  class="hasTip" title="'.JText::_('COM_EASYTABLEPRO_TABLE_FIELDSET_COL_POSITION_TT').'" /></td>');		// Position
		echo('<td><input type="text" value="'.$metaRow['label'].'" name="label'.$mRId.'" id="label'.$mRId.'" class="hasTip" title="'.JText::_('COM_EASYTABLEPRO_TABLE_FIELDSET_COL_LABEL_TT').'" /> <br />');	// label <br />
		if ($this->item->etet)
		{
			echo '<input type="hidden" name="origfieldalias'.$mRId.'" value="'.$metaRow['fieldalias'].
				'" /><input type="hidden" name="fieldalias'.$mRId.'" value="'.$metaRow['fieldalias'].'" />'.$metaRow['fieldalias'];
		}
		else
		{
			echo ('<span  class="hasTip" title="'.JText::_('COM_EASYTABLEPRO_TABLE_FIELDSET_COL_ALIAS_TT').
				'"><input type="hidden" name="origfieldalias'.$mRId.'" value="'.$metaRow['fieldalias'].'" />'.
				'<input type="text" name="fieldalias'.$mRId.'" value="'.$metaRow['fieldalias'].
				'" onchange="com_EasyTablePro.Table.validateAlias(this)" disabled="disabled" />'.
				'<img src="'.JURI::root().'media/com_easytablepro/images/locked.gif" onclick="com_EasyTablePro.Table.unlock(this, '.$mRId.
				');" id="unlock'.$mRId.'" alt="Unlock Alias" /></span></td>');		// alias
		}
		echo('<td><textarea cols="30" rows="2" name="description'.$mRId.'" class="hasTip" title="'.
			JText::_('COM_EASYTABLEPRO_TABLE_DESCRIPTION_TT').'" >'.$metaRow['description'].'</textarea></td>');				// Description
		echo('<td>'.$this->getTypeList($mRId, $metaRow['type']).'<br />'.'<input type="hidden" name="origfieldtype'.$mRId.
			'" value="'.$metaRow['type'].'" />'.'<input type="text" value="'.$this->getFieldOptions($metaRow['params']).'" name="fieldoptions'.
			$mRId.'" class="hasTip" title="'.JText::_('COM_EASYTABLEPRO_TABLE_FIELDSET_COL_OPTIONS_TT').'" /></td>');	// Type / Field Options

		$tdName          = 'list_view'.$mRId;
		$tdStart         = '<td align="center"><input type="hidden" name="'.$tdName.'" value="'.$metaRow['list_view'].'" />';	// List View Flag
		$tdFlagImg       = $this->getListViewImage($tdName, $metaRow['list_view']);
		$tdjs            = 'com_EasyTablePro.Table.toggleTick(\'list_view\', '.$mRId.');';
		$tdFlagImgLink   = '<a href="javascript:void(0);" onclick="'.$tdjs.'">'.$tdFlagImg.'</a>';
		echo($tdStart.$tdFlagImgLink.$tdEnd);

		$tdName          = 'detail_link'.$mRId;
		$tdStart         = '<td align="center"><input type="hidden" name="'.$tdName.'" value="'.$metaRow['detail_link'].'" />';	// Detail Link Flag
		$tdFlagImg       = $this->getListViewImage($tdName, $metaRow['detail_link']);
		$tdjs            = 'com_EasyTablePro.Table.toggleTick(\'detail_link\', '.$mRId.');';
		$tdFlagImgLink   = '<a href="javascript:void(0);" onclick="'.$tdjs.'">'.$tdFlagImg.'</a>';
		echo($tdStart.$tdFlagImgLink.$tdEnd);

		$tdName          = 'detail_view'.$mRId;
		$tdStart         = '<td align="center"><input type="hidden" name="'.$tdName.'" value="'.$metaRow['detail_view'].'" />';	// Detail View Flag
		$tdFlagImg       = $this->getListViewImage($tdName, $metaRow['detail_view']);
		$tdjs            = 'com_EasyTablePro.Table.toggleTick(\'detail_view\', '.$mRId.');';
		$tdFlagImgLink   = '<a href="javascript:void(0);" onclick="'.$tdjs.'">'.$tdFlagImg.'</a>';
		echo($tdStart.$tdFlagImgLink.$tdEnd);

		$tdName          = 'search_field'.$mRId;
		$tdParamsObj     = new JRegistry(); $tdParamsObj->loadString($metaRow['params']);
		$tdSearchField   = $tdParamsObj->get('search_field',1);
		$tdStart         = '<td align="center"><input type="hidden" name="'.$tdName.'" value="'.$tdSearchField.'" />';			// Search This Field
		$tdFlagImg       = $this->getListViewImage($tdName, $tdSearchField);
		$tdjs            = 'com_EasyTablePro.Table.toggleTick(\'search_field\', '.$mRId.');';
		$tdFlagImgLink   = '<a href="javascript:void(0);" onclick="'.$tdjs.'">'.$tdFlagImg.'</a>';
		echo($tdStart.$tdFlagImgLink.$tdEnd);

		echo "</tr>\r\r";                                                                                                             // Close the row
		$k = 1 - $k;
	}
	if (!$this->item->etet)
	{
		echo('<tr id="et_controlRow" class="et_controlRow-nodisplay"><td > <a href="javascript:void(0);" '.
			'onclick="com_EasyTablePro.Table.addField()"><img class="et_addField" src="'.JURI::root().
			'media/com_easytablepro/images/icon-add.png" alt="'.JText::_('COM_EASYTABLEPRO_TABLE_ADD_NEW_FIELD_LABEL').'" /></a>'.
			'<input type="hidden" name="newFlds" id="newFlds" value="" /><input type="hidden" name="deletedFlds" id="deletedFlds" value="" />'.
			'</td><td colspan="2"><a href="javascript:void(0);" onclick="com_EasyTablePro.Table.addField()">'.
			JText::_('COM_EASYTABLEPRO_TABLE_ADD_FIELD_BTN').'</a></td><td colspan="6"><em>'.
			JText::_('COM_EASYTABLEPRO_TABLE_ADD_NEW_FIELD_DESC').'</em></td></tr>');
	}
	$this->mRIds = $mRIds;
	?>
</tbody>
