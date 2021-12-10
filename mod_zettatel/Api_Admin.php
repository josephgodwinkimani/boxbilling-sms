<?php
/**
 * Zettatel BoxBilling module
 *
 * Module only consumes Admin API as of 10/12/2021
 *
 *
 * @copyright Copyright (c) 2021 Joseph Godwin Kimani (https://kimani.gocho.live)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version   $Id$
 */
class Box_Mod_Zettatel_Api_Admin extends Api_Abstract
{
    /**
     * Send SMS via Zettatel web service
     * 
     * @param string $userId - The registered username parameter to be passed. You can use this if apiKey is not being used.
     * @param string $password - The password needs to be urlencoded if there are any special characters used in the password field. You can use this if apiKey is not being used.
     * @param string $msg - sms text message
     * @param string $apikey - apiKey needs to be sent as HTTP header when you are not using userId and password credentials. You can avail this from your user control panel and use instead of userId and password HTTP Request parameter. Please do not disclose this to anyone.
     * @param string $sendMethod - Method needs to be defined as quick to send SMS in batches.
     * @param string $mobile - Mobile with country code.
     * @param string $msgType - Unicode for regional and text for English message content.
     * @param string $testMessage - Enable true to test your message and messages wont be delivered when enabled true.
     * @param string $scheduleTime - Date format YYYY-MM-DD HH:MM:SS
     *  
     * 
     * @return bool
     */
    public function send($data)
    {
        if(!isset($data['mobile'])) {
            throw new Box_Exception("Mobile number parameter (mobile) is missing");
        }
        
        if(!is_numeric($data['mobile'])) {
            throw new Box_Exception("Mobile number is not valid. Only numeric values are allowed");
        }
        
        if(!isset($data['msg'])) {
            throw new Box_Exception("SMS text is missing");
        }
        
        $mod = new Box_Mod('zettatel');
        $config = $mod->getConfig();
        
        $params = array(
            // LOGIN CREDENTIALS
            'userid'      =>  $config['userid'],
            'password'  =>  $config['password'],
            'apikey'   => $config['apikey'],
            // REQUIRED PARAMETERS
            'senderid'   => $config['senderid'], 
            'mobile'        =>  $data['mobile'],
            'msg'      =>  $data['msg'],           
            //'msgType'   => $config['msgType'],
            //'sendMethod'    =>  $config['sendMethod'],
        );

        /**
        *
        * The http_build_query() function used to generate URL-encoded query string from the associative (or indexed) array.
        * API Supports both POST and GET method over HTTP protocol.
        *
        */
        $url = 'https://portal.zettatel.com/SMSApi/send?'.http_build_query($params);
        
        $ret = file_get_contents($url);
        if(BB_DEBUG) error_log('Zettatel response: '.$ret);
        
        $sess = explode(":",$ret);
        if ($sess[0] != "ID") {
            throw new Box_Exception("Zettatel: :error", array(':error' => $ret));
        }
        
        $this->_log('Zettatel SMS %s to %s with text: %s', $ret, $data['to'], $data['text']);
        
        return true;
    }
}