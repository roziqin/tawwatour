<?php


function sendEmail($to,$subject,$message='',$view='',$from='',$fromName='')
{

  $CI =& get_instance();
    /*
     * Please check the setting first
     * Setting placed in application/config/email.php
     *
     */

        // Get full html:
        $body =
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset='.strtolower(config_item('charset')).'" />
                <title>'.html_escape($subject).'</title>
                <style type="text/css">
                    body {
                        font-family: Arial, Verdana, Helvetica, sans-serif;
                        font-size: 16px;
                    }
                </style>
            </head>
            <body>
            '.$message.'
            </body>
            </html>';
        if(empty($message)) {
            $message = '<p style="color:green">Halo test. This message has been sent for testing purposes.</p>';
        }
        if(!empty($view)) {
            $body = $view;
        }
        if(empty($from)) {
            $from = $CI->config->item('default_from_email');
        }
        if(empty($fromName)) {
            $fromName = $CI->config->item('default_from_name');
        }
        
        $CI->load->library('email');
        $result = $CI->email
           ->from($from,$fromName)
            ->reply_to($from,$fromName)    // Optional, an account where a human being reads.
            ->to($to)
            ->subject($subject)
            ->message($body)
            ->send();
        // var_dump($result);
        // echo '<br />';
        // echo $CI->email->print_debugger();

        // exit;
}