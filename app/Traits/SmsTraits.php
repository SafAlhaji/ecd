<?php

namespace App\Traits;

use App\Models\OrganizationDetails;
use App\Models\SmsGateway;

/**
 * It's main purpose is to manage media files on both public and storage.
 */
trait SmsTraits
{
    public $gateway_data;
    public $info;

    public function get_gateway_data()
    {
        $this->gateway_data = SmsGateway::find(1);

        return $this->gateway_data;
    }

    public function get_org_info()
    {
        $this->info = OrganizationDetails::find(1);

        return $this->info;
    }

    public function send_sms($number, $message)
    {
        //www.hisms.ws/api.php?send_sms&username=xx&password=xx&numbers=966xxxx,966xxxx&sender=xxx&message=xxx&date=2015-1-30&time=24:01
        //http://sms.malath.net.sa/httpSmsProvider.aspx?username=xxxxxx&password=xxxxxx&mobile=9665xxxxxx&unicode=U&message=062A062C063106280647002006450646002006450648064206390020062C064806270644&sender=your sender name here
        // dd(preg_match('/\p{Arabic}+/u', $text), preg_match('/\p{Arabic}+/u', $text1), preg_match('/\p{Bengali}+/u', $text2));
        // if (preg_match('/\p{Bengali}+/u', $message)) {
        //     // $str = strtoupper(str_replace(array('"', '\u'), array('',' '), json_encode($message)));
        //     // // dd($str);
        //     // $text_array = explode(' ', $str);
        //     // $final_text='';

        //     // foreach ($text_array as $value) {
        //     //     if ($value !== "") {
        //     //         $uni = '\u{000'.strtolower($value).'}'; // First bracket needs to be separated, otherwise you get '\u1F605'

        //     //         $final_text .= $uni;
        //     //         // $final_text .= "u$value";
        //     //     }
        //     //     if ($value == " ") {
        //     //         $final_text .= " ";
        //     //     }
        //     // }
        //     // dd($final_text);
        //     // dd($str = "\u{0986}\u{09b2}-\u{09ac}\u{09bf}\u{09b0}\u{09c1}\u{09a8}\u{09c0} \u{09b9}\u{09be}\u{09b8}\u{09aa}\u{09be}\u{09a4}\u{09be}\u{09b2} \u{09b2}\u{09bf}\u{09ae}\u{09bf}\u{099f}\u{09c7}\u{09a1}");
        // // dd($message[0], unpack('V', iconv('UTF-8', 'UCS-4LE', $message)));
        //     $final_text = preg_replace('/\s+/u', '-', trim($message));//($message);
        // } elseif (preg_match('/\p{Arabic}+/u', $message)) {
        //     $final_text = urlencode($message);
        // } else {
        //     $final_text = urlencode($message);
        // }
        $final_text = urlencode($message);
        // dd(($this->is_arabic($text1)));
        // if ($this->is_arabic($message)) {
        //     $final_text = $message;
        // }
        // // elseif (strlen($message) != mb_strlen($message, 'utf-8')) {
        // //     $str = strtoupper(str_replace(array('"', '\u'), array('',' '), json_encode($message)));
        // //     $text_array = explode(' ', $str);
        // //     $final_text='';
        // //     foreach ($text_array as $value) {
        // //         if ($value !== "") {
        // //             // $uni = '{' .  $value; // First bracket needs to be separated, otherwise you get '\u1F605'

        // //             $final_text .= "0000".strtolower($value);
        // //             // $final_text .= "u$value";
        // //         }
        // //         if ($value == " ") {
        // //             $final_text .= " ";
        // //         }
        // //     }
        // // }
        // else {
        //     $final_text = $message;
        // }

        $username = '';
        $password = '';
        $gateway_data = $this->get_gateway_data();
        $info = $this->get_org_info();
        $number = str_replace(' ', '', $number);
        $other_param = '';
        if ($gateway_data && $info) {
            foreach ($gateway_data->other_parameters as $key => $value) {
                $other_param .= implode('=', array_values($value)).'&';
            }
            $number = str_replace(' ', '', $number);
            if (OrganizationDetails::COUNTRY_KSA == $info->country_code || OrganizationDetails::COUNTRY_SUDAN == $info->country_code) {
                $number = '='.$info->country_code.ltrim($number, 0);
            } else {
                $number = '='.$info->country_code.$number;
            }
            if (SmsGateway::GET == $gateway_data->method) {
                $url = $gateway_data->url.'?'.$other_param;
                // $url .= $gateway_data->to_parameter_name.'=966'.ltrim($number, 0).'&';
                $url .= $gateway_data->to_parameter_name.$number.'&';
                $url .= $gateway_data->sender_parameter_name.'='.$gateway_data->sender_name.'&';
                $url .= $gateway_data->message_parameter_name.'="'.$final_text.'"';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                if ($gateway_data->secret_key) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                   'Authorization:Bearer '.$gateway_data->secret_key, ]);
                }
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseData = curl_exec($ch);
                if (curl_errno($ch)) {
                    return curl_error($ch);
                }
                curl_close($ch);
                // if (json_decode($responseData)->requestStatus->MessageIDs) {
                //    return 'Success';
                // }
                $response_data = json_decode($responseData);
                if (isset($response_data->requestStatus)) {
                    $response_data = $response_data->requestStatus;
                } else {
                    $response_data = 'failed';
                }

                // dd($response_data);

                return $response_data;
            }
            // if ($gateway_data->method == SmsGateway::POST) {

            //     $ch = curl_init();
            //     $fields = [$gateway_data->to_parameter_name => $number,
            //              $gateway_data->sender_parameter_name =>$sender,
            //         $gateway_data->message_parameter_name =>  $message];
            //     $postvars = '';
            //     foreach ($fields as $key=>$value) {
            //         $postvars .= $key . "=" . $value . "&";
            //     }
            //     $url = $gateway_data->url;
            //     curl_setopt($ch, CURLOPT_URL, $url);
            //     if ($gateway_data->secret_key) {
            //         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //        'Authorization:Bearer '.$gateway_data->secret_key));
            //     }
            //     curl_setopt($ch, CURLOPT_POST, 1);                //0 for a get request
            //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            //     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            //     $responseData = curl_exec($ch);
            //     if (curl_errno($ch)) {
            //         return curl_error($ch);
            //     }
            //     curl_close($ch);
            //     return $responseData;
            // }
        }
    }
}
