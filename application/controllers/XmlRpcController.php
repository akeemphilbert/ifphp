<?php
/**
 * XML-RPC Endpoint
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright Ifphp (c) 2009
 */

class XmlRpcController extends Zend_Controller_Action
{

    /**
	 * Ping should be called when new content is created
	 */
	public function pingAction()
    {
		//Do not render anything, otherwise there will be an xml parse error
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();

		try {

			//set up a new factory Zend xmlrpc server and add classes
	 		$server = new Zend_XmlRpc_Server();
	 		$server->setClass('Ifphp_Ping_XmlRpc','pingback');

	 		//success
	 		echo $server->handle();

 		} catch(Exception $e) {
 			throw $e;
 		}
	}
}
?>
