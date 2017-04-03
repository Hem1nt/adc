<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Resource_Post extends Fishpig_Wordpress_Model_Resource_Post_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post', 'ID');
	}

	/**
	 * Retrieve a collection of post tags
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return Fishpig_Wordpress_Model_Resource_Post_Tag_Collection
	 */
	public function getPostTags(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_tag_collection')
					->addPostIdFilter($post->getId());
	}
	
	/**
	 * Retrieve a collection of categories
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @retrun Fishpig_Wordpress_Model_Post_Category_Collection
	 */
	public function getParentCategories(Fishpig_Wordpress_Model_Post $post)
	{
		return Mage::getResourceModel('wordpress/post_category_collection')
			->addPostIdFilter($post->getId());
	}
	
	/**
	 * Retrieve a single parent category
	 * This is the category associated with the post with the lowest term_id
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @retrun Fishpig_Wordpress_Model_Post_Category_Collection
	 */
	public function getParentCategory(Fishpig_Wordpress_Model_Post $post)
	{
		$collection = Mage::getResourceModel('wordpress/post_category_collection')
			->addPostIdFilter($post->getId());
		
		$collection->getSelect()
			->limit(1)
			->reset(Zend_Db_Select::ORDER)
			->order('main_table.term_id ASC');
		
		$collection->load();
		
		return $collection->getFirstItem()->getId()
			? $collection->getFirstItem()
			: false;
	}
}
