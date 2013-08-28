<?php
/**
 * @version		1.0
 * @package		nuContent
 * @author		Nuevvo - http://nuevvo.com
 * @copyright	Copyright (c) 2010 - 2013 Nuevvo Webware Ltd. All rights reserved.
 * @license		http://nuevvo.com/license
 */


// no direct access
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldNuContentModuleTemplate extends JFormField
{

    var $type = 'NuContentModuleTemplate';

    function getInput()
    {
        jimport('joomla.filesystem.folder');
        $moduleName = (string)$this->element->attributes()->modulename;
        $moduleTemplatesPath = JPATH_SITE.'/modules/'.$moduleName.'/tmpl';
        $moduleTemplatesFolders = JFolder::folders($moduleTemplatesPath);
        $db = JFactory::getDBO();
        $query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
        $db->setQuery($query);
        $defaultemplate = $db->loadResult();
        $templatePath = JPATH_SITE.'/templates/'.$defaultemplate.'/html/'.$moduleName;
        if (JFolder::exists($templatePath))
        {
            $templateFolders = JFolder::folders($templatePath);
            $folders = @array_merge($templateFolders, $moduleTemplatesFolders);
            $folders = @array_unique($folders);
        }
        else
        {
            $folders = $moduleTemplatesFolders;
        }
        $exclude = 'Default';
        $options = array();
        foreach ($folders as $folder)
        {
            if (preg_match(chr(1).$exclude.chr(1), $folder))
            {
                continue;
            }
            $options[] = JHTML::_('select.option', $folder, $folder);
        }
        array_unshift($options, JHTML::_('select.option', 'Default', '-- '.JText::_('NUCONTENT_USE_DEFAULT').' --'));
        return JHTML::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'value', 'text', $this->value);
    }

}
