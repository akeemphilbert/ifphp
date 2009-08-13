<?php
/**
 * This baes controller sets up syndicate functionality for list actions.
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_SyndicateController extends Zend_Controller_Action
{
    /**
     *
     * @var Zend_Controller_Action_Helper_ContextSwitch
     */
    protected $_contextSwitch;
    /**
     * Setup the necessary contexts
     */
    public function init()
    {
        parent::init();
        
        $this->_contextSwitch = $this->_helper->getHelper('contextSwitch');
        $this->_contextSwitch->addContext('rss',array('suffix'=>'rss','headers'=>array('Content-type'=>'application/rss+xml')));
        $this->_contextSwitch->addContext('atom',array('suffix'=>'atom','headers'=>array('Content-type'=>'application/atom+xml')));

        $this->_contextSwitch->addActionContext('list',array('rss','atom'))
                             ->addActionContext('index', array('rss','atom'))
                             ->initContext();

    }
}