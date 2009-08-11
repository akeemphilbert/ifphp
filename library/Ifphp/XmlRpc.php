<?php
/**
 * XML-RPC classes
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 */
class Ifphp_PingBack_XmlRpc
{
    /**
     * Notifies the server that a link has been added to sourceURI, pointing to targetURI.
     *
     * @param string $sourceURI The absolute URI of the post on the source page containing the link to the target site.
     * @param string $targetURI The absolute URI of the target of the link, as given on the source page.
     *
     * @return string
     */
    public function ping($sourceURI,$targetURI)
    {
        try
        {

        }
        catch (Zend_Exception $error)
        {

        }
        return 'successful';
    }
}
