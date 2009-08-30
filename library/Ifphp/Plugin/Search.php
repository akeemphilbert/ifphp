<?php
/**
 * Search Plugin responsible for updating the search index
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_Plugin_Search extends Ifphp_Controller_Plugin_Abstract
{
    public function addPost(Post $post,$feed)
    {
        $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->post);

        $feed = $post->findParentFeeds();
        
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('pid', $post->id));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $post->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
        $doc->addField(Zend_Search_Lucene_Field::Text('link', $post->link));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedTitle', $feed->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedSlug', $feed->slug));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedDescription', $feed->description));
        $doc->addField(Zend_Search_Lucene_Field::keyword('category', $feed->findParentCategories()->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('description', $post->description));
        $doc->addField(Zend_Search_Lucene_Field::unIndexed('publishDate', $post->publishDate));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('type','post'));
        $index->addDocument($doc);

    }

    public function addFeed(Feed $feed)
    {
        $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->feed);

        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('pid', $feed->id));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $feed->url));
        $doc->addField(Zend_Search_Lucene_Field::Text('language', $feed->language));
        $doc->addField(Zend_Search_Lucene_Field::Text('category', $feed->category));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $feed->description));
        $index->addDocument($doc);
    }
}