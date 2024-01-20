<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
trait Setting{
    function getDefaultSystemSetting(){
        return $singleArray=[
            'SMTP_EMAIL'        =>env('MAIL_USERNAME'),
            'SMTP_PASSWORD'     =>env('MAIL_PASSWORD'),
            'SMTP_HOST'         =>env('MAIL_HOST'),
            'SMTP_PORT'         =>env('MAIL_PORT'),
            'SMTP_MAILER'       =>env('MAIL_MAILER'),
            'SMTP_NAME'         =>env('MAIL_FROM_NAME'),
            'SMTP_SSL'          =>env('MAIL_SSL'),
            'SMTP_DEFAULT'      =>env('MAIL_DEFAULT'),

            'SMS_URL'           =>env('SMS_URL'),
            'SMS_SENDER'        =>env('SMS_SENDER'),
            'SMS_USERID'        =>env('SMS_USERID'),
            'SMS_PASSWORD'      =>env('SMS_PASSWORD'),

            'FCM_API'           =>env('FCM_API'),

            'CCAVENUE_MERCHANT_ID'  =>env('CCAVENUE_MERCHANT_ID'),
            'CCAVENUE_WORKING_KEY'  =>env('CCAVENUE_WORKING_KEY'),
            'CCAVENUE_ACCESS_CODE'  =>env('CCAVENUE_ACCESS_CODE'),
            'PAYPAL_CLIENT_ID'      =>env('CCAVENUE_MERCHANT_ID'),
            'CCAVENUE_WORKING_KEY'  =>env('CCAVENUE_WORKING_KEY'),
            'CCAVENUE_ACCESS_CODE'  =>env('CCAVENUE_ACCESS_CODE'),
            'PAYPAL_MODE'               =>env('PAYPAL_MODE'),
            'PAYPAL_SANDBOX_CLIENT_ID'  =>env('PAYPAL_SANDBOX_CLIENT_ID'),
            'PAYPAL_SANDBOX_CLIENT_SECRET' =>env('PAYPAL_SANDBOX_CLIENT_SECRET'),
            'PAYPAL_CURRENCY'               =>env('PAYPAL_CURRENCY'),
        ];
    }
    function sendSmsOTP($phonewithcountry,$message,$SMSUserID,$SMSPassword,$SMSSenderId,$SMSUrl){
        $postData = array(
            'mobile' => $SMSUserID,
            'pass' => $SMSPassword,
            'senderid' => $SMSSenderId,
            'to' => $phonewithcountry,
            'msg' => $message
		);
		$url=$SMSUrl; //"https://www.smsidea.co.in/smsstatuswithid.aspx";/* API URL*/
		$ch = curl_init();/* init the resource */
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData
		));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);/* Ignore SSL certificate verification */
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$output = curl_exec($ch);/* get response */
        // if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        // }
		curl_close($ch);	
    }
}