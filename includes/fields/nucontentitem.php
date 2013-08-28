<?php
/**
 * @version		1.0
 * @package		nuContent Lite
 * @author		Nuevvo - http://nuevvo.com
 * @copyright	Copyright (c) 2010 - 2013 Nuevvo Webware Ltd. All rights reserved.
 * @license		http://nuevvo.com/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldNuContentItem extends JFormField
{

	var $type = 'NuContentItem';

	public function getInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/modules/mod_nucontent_lite/includes/css/fields.nucontent.css');
		self::loadjQuery(true);
		$row = JTable::getInstance('Content', 'JTable');
		$html = '';
		$script = '';
		if ($this->multiple)
		{
			$html .= '<div class="fltlft nuContentField">';
			$html .= '  <ul id="nuContentItemList" class="nuContentSortable">';
			if (is_array($this->value))
			{
				foreach ($this->value as $id)
				{
					$row->load($id);
					$html .= '<li>';
					$html .= '  <img onclick="$nuContent(this).parent().remove();" class="nuContentRemoveButton" src="'.JURI::root(true).'/modules/mod_nucontent_lite/includes/images/remove.png" alt="'.JText::_('NUCONTENT_REMOVE_ENTRY_FROM_LIST').'" />';
					$html .= '	 <span class="nuContentSortHandle">'.$row->title.'</span>';
					$html .= '	 <input type="hidden" value="'.$row->id.'" name="'.$this->name.'"/>';
					$html .= '	 <span class="nuContentClear"></span>';
					$html .= '</li>';
				}
			}
			$html .= '  </ul>';
			$html .= '</div>';
			$script .= "
				function jSelectArticle_".$this->id."(id, title, catid, object) {
					var exists = false;
					\$nuContent('#nuContentItemList input').each(function(){
							if(\$nuContent(this).val()==id){
								alert('".JText::_('NUCONTENT_THE_SELECTED_ITEM_IS_ALREADY_IN_THE_LIST')."');
								exists = true;
							}
					});
					if(!exists){
						var container = \$nuContent('<li/>').appendTo(\$nuContent('#nuContentItemList'));
						var img = \$nuContent('<img/>',{class:'nuContentRemoveButton', src:'".JURI::root(true)."/modules/mod_nucontent_lite/includes/images/remove.png'}).appendTo(container);
						img.click(function(){\$nuContent(this).parent().remove();});
						var span = \$nuContent('<span/>',{class:'nuContentSortHandle'}).html(title).appendTo(container);
						var input = \$nuContent('<input/>',{value:id, type:'hidden', name:'".$this->name."'}).appendTo(container);
						var span = \$nuContent('<span/>',{class:'nuContentClear'}).appendTo(container);
						\$nuContent('#nuContentItemList').sortable('refresh');
						alert('".JText::_('NUCONTENT_ITEM_ADDED_IN_THE_LIST', true)."');
					}
				}
				
				\$nuContent(document).ready(function(){
					\$nuContent('#nuContentItemList').sortable({
						containment: '#nuContentItemList',
						items: 'li',
						handle: 'span.nuContentSortHandle'
					});
					\$nuContent('body').css('overflow-y', 'scroll');
					\$nuContent('#nuContentItemList .remove').click(function(){
						\$nuContent(this).parent().remove();
					});
				});
			";
		}
		else
		{
			$row->load($this->value);
			$title = $row->title;
			if (empty($title))
			{
				$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
			}
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
			$html .= '<div class="fltlft">';
			$html .= '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
			$html .= '</div>';
			$script .= '	function jSelectArticle_'.$this->id.'(id, title, catid, object) {';
			$script .= '		document.id("'.$this->id.'_id").value = id;';
			$script .= '		document.id("'.$this->id.'_name").value = title;';
			$script .= '		SqueezeBox.close();';
			$script .= '	}';
			$class = '';
			if ($this->required)
			{
				$class = ' class="required modal-value"';
			}
			$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$this->value.'" />';
		}
		$document->addScriptDeclaration($script);
		return $html;

	}

	public function getLabel()
	{
		$label = strip_tags(parent::getLabel());
		JHtml::_('behavior.modal', 'a.modal');
		$link = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$this->id;
		$html = '<div class="button2-left nuContentLabel">';
		$html .= '  <div class="blank">';
		$html .= '	<a class="modal btn" title="'.JText::_($this->description).'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.$label.'</a>';
		$html .= '  </div>';
		$html .= '</div>';

		return $html;
	}

	public static function loadjQuery($ui = false)
	{
		$document = JFactory::getDocument();
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			JHtml::_('jquery.framework');
			if ($ui)
			{
				JHtml::_('jquery.ui', array(
					'core',
					'sortable'
				));
			}
		}
		else
		{
			$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
			if ($ui)
			{
				$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
			}
		}
		$document->addScript(JURI::root(true).'/modules/mod_nucontent_lite/includes/js/nucontent.noconflict.js');

	}

}
