<?php
/**
 * @version		1.0
 * @package		nuContent Lite
 * @author		Nuevvo - http://nuevvo.com
 * @copyright	Copyright (c) 2010 - 2013 Nuevvo Webware Ltd. All rights reserved.
 * @license		http://nuevvo.com/license
 */

defined('_JEXEC') or die ; ?>

<div id="nuContentModuleBox<?php echo $module->id; ?>" class="nuContentItemsBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">

	<?php if($params->get('itemPreText')): ?>
	<p class="modulePretext"><?php echo $params->get('itemPreText'); ?></p>
	<?php endif; ?>

	<?php if(count($items)): ?>
  <ul>
    <?php foreach ($items as $key=>$item):	?>
    <li class="<?php echo ($key%2) ? "odd" : "even"; if(count($items)==$key+1) echo ' lastItem'; ?>">

      <?php if($params->get('itemAuthorAvatar')): ?>
      <span class="nuContentAvatar moduleItemAuthorAvatar">
		<img src="<?php echo $item->authorAvatar; ?>" alt="<?php echo $item->author; ?>" style="width:<?php echo $params->get('itemAuthorAvatarWidth', 50); ?>px;height:auto;" />
	  </span>
      <?php endif; ?>

      <?php if($params->get('itemTitle')): ?>
      <a class="moduleItemTitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
      <?php endif; ?>

      <?php if($params->get('itemAuthor')): ?>
      <div class="moduleItemAuthor"><?php echo $item->author; ?></div>
	  <?php endif; ?>

      <!-- Plugins: AfterDisplayTitle -->
      <?php echo $item->event->afterDisplayTitle; ?>

      <!-- Plugins: BeforeDisplayContent -->
      <?php echo $item->event->beforeDisplayContent; ?>

      <?php if($params->get('itemImage') || $params->get('itemIntroText')): ?>
      <div class="moduleItemIntrotext">
	      <?php if($params->get('itemImage') && $item->image): ?>
	      <a class="moduleItemImage" href="<?php echo $item->link; ?>" title="<?php echo JText::_('NUCONTENT_CONTINUE_READING'); ?> &quot;<?php echo JFilterOutput::cleanText($item->title); ?>&quot;">
	      	<img src="<?php echo $item->image; ?>" alt="<?php echo $item->title; ?>" <?php echo ($params->get('itemImageWidth'))? 'width="'.$params->get('itemImageWidth').'"':''; ?>/>
	      </a>
	      <?php endif; ?>

      	<?php if($params->get('itemIntroText')): ?>
      	<?php echo $item->introtext; ?>
      	<?php endif; ?>
      </div>
      <?php endif; ?>

      <div class="clr"></div>

      <!-- Plugins: AfterDisplayContent -->
      <?php echo $item->event->afterDisplayContent; ?>

      <?php if($params->get('itemDateCreated')): ?>
      <span class="moduleItemDateCreated"><?php echo JText::_('NUCONTENT_WRITTEN_ON') ;?> <?php echo JHTML::_('date', $item->created, JText::_('NUCONTENT_DATE_FORMAT_LC2')); ?></span>
      <?php endif; ?>

      <?php if($params->get('itemCategory')): ?>
      <?php echo JText::_('NUCONTENT_IN') ;?> <a class="moduleItemCategory" href="<?php echo $item->categoryLink; ?>"><?php echo $item->categoryTitle; ?></a>
      <?php endif; ?>
      
      <?php if($params->get('itemTags') && count($item->nuTags)>0): ?>
      <div class="moduleItemTags">
      	<b><?php echo JText::_('NUCONTENT_TAGS'); ?>:</b>
        <?php foreach ($item->nuTags as $tag): ?>
        <a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

			<?php if($params->get('itemHits')): ?>
			<span class="moduleItemHits">
				<?php echo JText::_('NUCONTENT_READ'); ?> <?php echo $item->hits; ?> <?php echo JText::_('NUCONTENT_TIMES'); ?>
			</span>
			<?php endif; ?>

			<?php if($params->get('itemReadMore') && $item->fulltext): ?>
			<a class="moduleItemReadMore" href="<?php echo $item->link; ?>">
				<?php echo JText::_('NUCONTENT_READ_MORE'); ?>
			</a>
			<?php endif; ?>


      <div class="clr"></div>
    </li>
    <?php endforeach; ?>
    <li class="clearList"></li>
  </ul>
  <?php endif; ?>

	<?php if($params->get('itemCustomLink')): ?>
	<a class="moduleCustomLink" href="<?php echo $params->get('itemCustomLinkURL'); ?>" title="<?php echo JFilterOutput::cleanText($params->get('itemCustomLinkTitle')); ?>">
		<?php echo $params->get('itemCustomLinkTitle'); ?>
	</a>
	<?php endif; ?>

</div>