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

class JFormFieldNuContentHeader extends JFormField
{
	var $type = 'NuContentHeader';

	function getInput()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/modules/mod_nucontent_lite/includes/css/fields.nucontent.css');
		return '<div class="nuContentParamHeader">'.JText::_($this->value).'</div>';
	}

	function getLabel()
	{
		return '';
	}

}
