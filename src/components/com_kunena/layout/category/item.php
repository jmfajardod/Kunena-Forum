<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Item
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutCategoryItem
 *
 * @since  K4.0
 *
 */
class KunenaLayoutCategoryItem extends KunenaLayout
{
	/**
	 * Method to display categories Index sublayout
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayCategories()
	{
		if ($this->sections)
		{
			$this->subcategories = true;
			echo $this->subLayout('Category/Index')->setProperties($this->getProperties())->setLayout('subcategories');
		}
	}

	/**
	 * Method to display category action sublayout
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayCategoryActions()
	{
		if (!$this->category->isSection())
		{
			echo $this->subLayout('Category/Item/Actions')->setProperties($this->getProperties());
		}
	}

	/**
	 * Method to return array of actions sublayout
	 *
	 * @return array|boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function getCategoryActions()
	{
		$category = $this->category;
		$token    = '&' . \Joomla\CMS\Session\Session::getFormToken() . '=1';
		$actions  = array();

		// Is user allowed to post new topic?
		$url             = $category->getNewTopicUrl();
		$this->ktemplate = KunenaFactory::getTemplate();
		$topicicontype   = $this->ktemplate->params->get('topicicontype');
		$config          = KunenaConfig::getInstance();

		if ($config->read_only)
		{
			return false;
		}

		if ($category->isAuthorised('topic.create'))
		{
			if ($url && $topicicontype == 'B3')
			{
				$actions['create'] = $this->subLayout('Widget/Button')
					->setProperties(array('url' => $url, 'name' => 'create', 'scope' => 'topic', 'type' => 'communication', 'success' => true,
						'icon' => 'glyphicon glyphicon-edit glyphicon-white')
					);
			}
			elseif ($url && $topicicontype == 'fa')
			{
				$actions['create'] = $this->subLayout('Widget/Button')
					->setProperties(array('url' => $url, 'name' => 'create', 'scope' => 'topic', 'type' => 'communication', 'success' => true,
						'icon' => 'fa fa-pencil')
					);
			}
			else
			{
				$actions['create'] = $this->subLayout('Widget/Button')
					->setProperties(array('url' => $url, 'name' => 'create', 'scope' => 'topic', 'type' => 'communication', 'success' => true,
						'icon' => 'icon-edit icon-white')
					);
			}
		}

		if ($category->getTopics() > 0)
		{
			// Is user allowed to mark forums as read?
			$url = $category->getMarkReadUrl();

			if ($this->me->exists() && $this->total)
			{
				if ($url && $topicicontype == 'B3')
				{
					$actions['markread'] = $this->subLayout('Widget/Button')
						->setProperties(array('url' => $url, 'name' => 'markread', 'scope' => 'category', 'type' => 'user',
							'icon' => 'glyphicon glyphicon-check')
						);
				}
				elseif ($url && $topicicontype == 'fa')
				{
					$actions['markread'] = $this->subLayout('Widget/Button')
						->setProperties(array('url' => $url, 'name' => 'markread', 'scope' => 'category', 'type' => 'user', 'icon' => 'fa fa-book'));
				}
				else
				{
					$actions['markread'] = $this->subLayout('Widget/Button')
						->setProperties(array('url' => $url, 'name' => 'markread', 'scope' => 'category', 'type' => 'user', 'icon' => 'icon-drawer'));
				}
			}

			// Is user allowed to subscribe category?
			if ($category->isAuthorised('subscribe'))
			{
				$subscribed = $category->getSubscribed($this->me->userid);

				if ($url && $topicicontype == 'B3')
				{
					if (!$subscribed)
					{
						$url                  = "index.php?option=com_kunena&view=category&task=subscribe&catid={$category->id}{$token}";
						$actions['subscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'subscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'glyphicon glyphicon-bookmark')
							);
					}
					else
					{
						$url                    = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$category->id}{$token}";
						$actions['unsubscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'unsubscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'glyphicon glyphicon-bookmark')
							);
					}
				}
				elseif ($url && $topicicontype == 'fa')
				{
					if (!$subscribed)
					{
						$url                  = "index.php?option=com_kunena&view=category&task=subscribe&catid={$category->id}{$token}";
						$actions['subscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'subscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'fa fa-bookmark')
							);
					}
					else
					{
						$url                    = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$category->id}{$token}";
						$actions['unsubscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'unsubscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'fa fa-bookmark-o')
							);
					}
				}
				else
				{
					if (!$subscribed)
					{
						$url                  = "index.php?option=com_kunena&view=category&task=subscribe&catid={$category->id}{$token}";
						$actions['subscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'subscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'icon-bookmark')
							);
					}
					else
					{
						$url                    = "index.php?option=com_kunena&view=category&task=unsubscribe&catid={$category->id}{$token}";
						$actions['unsubscribe'] = $this->subLayout('Widget/Button')
							->setProperties(array('url' => $url, 'name' => 'unsubscribe', 'scope' => 'category', 'type' => 'user',
								'icon' => 'icon-bookmark')
							);
					}
				}
			}
		}

		return $actions;
	}

	/**
	 * Method to get the last post link
	 *
	 * @param   KunenaForumCategory $category The KunenaCategory object
	 * @param   string              $content  The content of last topic subject
	 * @param   string              $title    The title of the link
	 * @param   string              $class    The class attribute of the link
	 *
	 * @param   int                 $length
	 *
	 * @param   bool                $follow
	 * @param   bool                $canonical
	 *
	 * @return string
	 * @see   KunenaLayout::getLastPostLink()
	 * @since Kunena
	 */
	public function getLastPostLink($category, $content = null, $title = null, $class = null, $length = 20, $follow = true, $canonical = false)
	{
		$lastTopic = $category->getLastTopic();
		$channels  = $category->getChannels();

		if (!isset($channels[$lastTopic->category_id]))
		{
			$category = $lastTopic->getCategory();
		}

		$uri = $lastTopic->getUri($category, 'last');

		if (!$content)
		{
			$content = KunenaHtmlParser::parseText($category->getLastTopic()->subject, $length);
		}

		if ($title === null)
		{
			$title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastTopic()->subject));
		}

		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	/**
	 * Return the links of pagination item
	 *
	 * @param   int $maxpages The maximum number of pages
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getPagination($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination->getPagesLinks();
	}
}
