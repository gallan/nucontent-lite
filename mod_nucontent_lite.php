<?php
/**
 * @version		1.0
 * @package		nuContent Lite
 * @author		Nuevvo - http://nuevvo.com
 * @copyright	Copyright (c) 2010 - 2013 Nuevvo Webware Ltd. All rights reserved.
 * @license		http://nuevvo.com/license
 */

defined('_JEXEC') or die ;

require_once (dirname(__FILE__).'/helper.php');
modNuContentLiteHelper::loadCSS($params);
$items = modNuContentLiteHelper::getItems($params);
if (count($items))
{
	require (JModuleHelper::getLayoutPath('mod_nucontent_lite', $params->get('getTemplate', 'Default').'/default'));
}
