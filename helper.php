<?php
/**
 * @version		1.0
 * @package		nuContent Lite
 * @author		Nuevvo - http://nuevvo.com
 * @copyright	Copyright (c) 2010 - 2013 Nuevvo Webware Ltd. All rights reserved.
 * @license		http://nuevvo.com/license
 */

defined('_JEXEC') or die ;

class modNuContentLiteHelper
{

	public static function loadCSS($params)
	{
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		if (JFile::exists(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/html/mod_nucontent_lite/'.$params->get('getTemplate', 'Default').'/css/style.css'))
		{
			$document->addStyleSheet(JURI::root(true).'/templates/'.$mainframe->getTemplate().'/html/mod_nucontent_lite/'.$params->get('getTemplate', 'Default').'/css/style.css');
		}
		else
		{
			$document->addStyleSheet(JURI::root(true).'/modules/mod_nucontent_lite/tmpl/'.$params->get('getTemplate', 'Default').'/css/style.css');
		}
	}

	public static function prepareItem(&$item)
	{
		require_once (JPATH_SITE.'/components/com_content/helpers/route.php');
		$user = JFactory::getUser();
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorized = JAccess::getAuthorisedViewLevels($user->get('id'));
		if ($item->created_by_alias)
		{
			$item->author = $item->created_by_alias;
		}
		$item->title = JFilterOutput::ampReplace($item->title);
		$item->slug = $item->id.':'.$item->alias;
		$item->catslug = $item->catid.':'.$item->category_alias;
		if ($access || in_array($item->access, $authorized))
		{
			$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
		}
		else
		{
			$item->link = JRoute::_('index.php?option=com_users&view=login');
		}
		if (!isset($item->readmore_link))
		{
			$item->readmore_link = $item->link;
		}
		$item->categoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
		$item->image = null;
		$item->nuTags = array();
	}

	public static function getItemImage(&$item)
	{
		// Look first for the article images
		if (isset($item->images) && $item->images)
		{
			$images = json_decode($item->images);
			if ($images->image_intro)
			{
				$item->image = $images->image_intro;
				return;
			}
			if ($images->image_fulltext)
			{
				$item->image = $images->image_fulltext;
				return;
			}
		}

		// Get the first image from the content
		if (isset($item->fulltext))
		{
			$image = self::getFirstImage($item->introtext.$item->fulltext);
		}
		else
		{
			$image = self::getFirstImage($item->introtext);
		}
		if ($image)
		{
			$item->image = $image['src'];
			$item->introtext = JString::str_ireplace($image['tag'], '', $item->introtext);
			if (isset($item->fulltext))
			{
				$item->fulltext = JString::str_ireplace($image['tag'], '', $item->fulltext);
			}
			if (isset($item->text))
			{
				$item->text = JString::str_ireplace($image['tag'], '', $item->text);
			}

		}
		return;

	}

	public static function getFirstImage($buffer)
	{
		$regex = "#<img.+?>#s";
		if (preg_match_all($regex, $buffer, $matches, PREG_PATTERN_ORDER) > 0)
		{
			$image = array();
			$image['tag'] = $matches[0][0];
			$srcPattern = "#src=\".+?\"#s";
			if (preg_match($srcPattern, $matches[0][0], $match))
			{
				$image['src'] = str_replace('src="', '', $match[0]);
				$image['src'] = str_replace('"', '', $image['src']);
				return $image;
			}
		}
	}

	public static function getItemTags(&$item)
	{

		if (version_compare(JVERSION, '3.1', 'ge'))
		{
			jimport('cms.helper.tags');
			$helper = new JHelperTags;
			$item->nuTags = $helper->getItemTags('com_content.article', $item->id);
			if (count($item->nuTags))
			{
				require_once JPATH_SITE.'/components/com_tags/helpers/route.php';
				foreach ($item->nuTags as $tag)
				{
					$tag->name = $tag->title;
					$tag->link = JRoute::_(TagsHelperRoute::getTagRoute($tag->tag_id.':'.$tag->alias));
				}
				return;
			}
		}

		if ($item->metakey)
		{
			$tags = array();
			if (JString::strpos($item->metakey, ','))
			{
				$tags = explode(',', $item->metakey);
			}
			else
			{
				$tags[] = $item->metakey;
			}
			foreach ($tags as $tag)
			{
				$object = new stdClass;
				$object->name = JString::trim($tag);
				$object->link = JRoute::_('index.php?option=com_search&view=search&searchword='.$object->name);
				$item->nuTags[] = $object;
			}
		}
	}

	public static function getGravatar($email, $alias, $params)
	{
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		if (JFile::exists(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/images/placeholder/user.png'))
		{
			$image = 'templates/'.$mainframe->getTemplate().'/images/placeholder/user.png';
		}
		else
		{
			$image = 'media/nucontent/assets/images/user/default.png';
		}
		if ($alias != '')
		{
			$avatar = JURI::root(true).'/'.$image;
		}
		else
		{
			$avatar = 'http://www.gravatar.com/avatar/'.md5($email).'?s='.$params->get('itemAuthorAvatarWidth', 100).'&amp;default='.urlencode(JURI::root().$image);
		}
		return $avatar;
	}

	public static function wordLimit($string, $limit = 100, $endCharacter = '&#8230;')
	{
		if (JString::trim($string) == '')
		{
			return $string;
		}
		$string = strip_tags($string);
		$find = array(
			"/\r|\n/",
			"/\t/",
			"/\s\s+/"
		);
		$replace = array(
			" ",
			" ",
			" "
		);
		$string = preg_replace($find, $replace, $string);
		preg_match('/\s*(?:\S*\s*){'.(int)$limit.'}/', $string, $matches);
		if (strlen($matches[0]) == strlen($string))
		{
			$endCharacter = '';
		}
		return JString::rtrim($matches[0]).$endCharacter;
	}

	public static function getItems($params)
	{

		jimport('joomla.application.component.model');
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');

		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Common model states
		$appParams = $app->getParams();
		$model->setState('params', $appParams);
		$model->setState('list.select', 'a.id, a.title, a.alias, a.introtext, a.fulltext, a.checked_out, a.checked_out_time, a.catid, a.created, a.created_by, a.created_by_alias, CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, a.modified_by, uam.name as modified_by_name, CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up, a.publish_down, a.attribs, a.images, a.metadata, a.metakey, a.metadesc, a.access, a.hits, a.xreference, a.featured, LENGTH(a.fulltext) AS readmore, c.title AS categoryTitle');
		$model->setState('list.start', 0);
		$model->setState('filter.published', 1);
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorized = JAccess::getAuthorisedViewLevels($user->get('id'));
		$model->setState('filter.access', $access);
		$model->setState('filter.language', $app->getLanguageFilter());

		// Specific items model states
		if ($params->get('source') == 'specific')
		{
			$model->setState('filter.article_id', $params->get('items'));
		}
		// Filter by categories model states
		else
		{
			// Set limit
			$model->setState('list.limit', $params->get('itemCount', 5));

			// Categories filter
			if ($params->get('category_id'))
			{
				$categories = array();
				$categoriesModel = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categoriesModel->setState('params', $appParams);
				foreach ($params->get('category_id') as $catid)
				{
					$categories[] = $catid;
					if ($params->get('getChildren'))
					{
						$categoriesModel->setState('filter.parentId', $catid);
						$children = $categoriesModel->getItems(true);
						foreach ($children as $child)
						{
							$categories[] = $child->id;
						}
					}
				}
				$categories = array_unique($categories);
				JArrayHelper::toInteger($categories);
				$model->setState('filter.category_id', $categories);
			}

			// Featured filter
			switch ($params->get('featuredItems'))
			{
				case '2' :
					$model->setState('filter.featured', 'only');
					break;
				case '0' :
					$model->setState('filter.featured', 'hide');
					break;
				default :
					$model->setState('filter.featured', 'show');
					break;
			}

			// Set ordering
			switch ($params->get('itemsOrdering',''))
			{

				case 'date' :
					$ordering = 'a.created';
					$dir = 'ASC';
					break;

				case 'rdate' :
					$ordering = 'a.created';
					$dir = 'DESC';
					break;

				case 'alpha' :
					$ordering = 'a.title';
					$dir = 'ASC';
					break;

				case 'ralpha' :
					$ordering = 'a.title';
					$dir = 'DESC';
					break;

				case 'order' :
					if ($params->get('FeaturedItems') == '2')
					{
						$ordering = 'fp.ordering';
					}
					else
					{
						$ordering = 'a.ordering';
					}
					$dir = 'ASC';
					break;

				case 'rorder' :
					if ($params->get('FeaturedItems') == '2')
					{
						$ordering = 'fp.ordering';
					}
					else
					{
						$ordering = 'a.ordering';
					}
					break;

				case 'hits' :
					if ($params->get('popularityRange'))
					{
						$model->setState('filter.date_filtering', 'relative');
						$model->setState('filter.date_field', 'a.created');
						$model->setState('filter.relative_date', (int)$params->get('popularityRange'));
					}
					$ordering = 'a.hits';
					$dir = 'DESC';
					break;

				case 'rand' :
					$ordering = 'RAND()';
					$dir = '';
					break;

				case 'best' :
					$ordering = 'rating';
					$dir = 'DESC';
					break;

				case 'modified' :
					$ordering = 'a.modified';
					$dir = 'DESC';
					break;

				default :
					$ordering = 'a.id';
					$dir = 'DESC';
					break;
			}

			$model->setState('list.ordering', $ordering);
			$model->setState('list.direction', $dir);

		}

		$items = $model->getItems();

		// Sort the items in case user has selected specific ones
		if ($params->get('source') == 'specific')
		{
			$sort = array();
			foreach ($items as $item)
			{
				$key = array_search($item->id, $params->get('items'));
				$sort[$key] = $item;
			}
			ksort($sort, SORT_NUMERIC);
			$items = $sort;
		}

		if (count($items))
		{

			foreach ($items as $item)
			{

				self::prepareItem($item);

				// Introtext
				$item->text = '';
				if ($params->get('itemIntroText'))
				{
					// Word limit
					if ($params->get('itemIntroTextWordLimit'))
					{
						$item->text .= self::wordLimit($item->introtext, $params->get('itemIntroTextWordLimit'));
					}
					else
					{
						$item->text .= $item->introtext;
					}
				}

				//Plugins
				if ($params->get('JPlugins'))
				{

					$dispatcher = JDispatcher::getInstance();
					JPluginHelper::importPlugin('content');

					$results = $dispatcher->trigger('onContentPrepare', array(
						'mod_nucontent.article',
						&$item,
						&$params,
						0
					));

					$item->event = new stdClass();
					$results = $dispatcher->trigger('onContentAfterTitle', array(
						'mod_nucontent.article',
						&$item,
						&$params,
						0
					));
					$item->event->afterDisplayTitle = trim(implode("\n", $results));

					$results = $dispatcher->trigger('onContentBeforeDisplay', array(
						'mod_nucontent.article',
						&$item,
						&$params,
						0
					));
					$item->event->beforeDisplayContent = trim(implode("\n", $results));

					$results = $dispatcher->trigger('onContentAfterDisplay', array(
						'mod_nucontent.article',
						&$item,
						&$params,
						0
					));
					$item->event->afterDisplayContent = trim(implode("\n", $results));

				}
				// Even if plugins are disabled we may need to fetch the images, author avatar and tags depending in the module settings
				if ($params->get('itemImage'))
				{
					self::getItemImage($item);
				}
				if ($params->get('itemTags'))
				{
					self::getItemTags($item);
				}
				if ($params->get('itemAuthorAvatar'))
				{
					$item->authorAvatar = self::getGravatar($item->author_email, $item->created_by_alias, $params);
				}

				$item->introtext = $item->text;
				$rows[] = $item;
			}

			return $rows;

		}

	}

}
