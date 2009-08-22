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
        $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->search->post);

        $feed = $post->findParentFeeds();
        
        $doc = new Zend_Search_Lucene_Document();
        
        $doc->addField(Zend_Search_Lucene_Field::Text('pid', $post->id));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $post->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $post->siteUrl));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $post->url));
        $doc->addField(Zend_Search_Lucene_Field::Text('language', $post->language));
        $doc->addField(Zend_Search_Lucene_Field::Text('category', $post->category));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $post->description));
        $index->addDocument($doc);

    }

    public function addFeed(Feed $feed)
    {
        $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->search->feed);

        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
        $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
        $doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $feed->url));
        $doc->addField(Zend_Search_Lucene_Field::Text('language', $feed->language));
        $doc->addField(Zend_Search_Lucene_Field::Text('category', $feed->category));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $feed->description));
        $index->addDocument($doc);
    }
}