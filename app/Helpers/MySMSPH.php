<?php

namespace App\Helpers;

class MySMSPH
{

    /****************************************************************************************************
     * Send SMS using MySMSPH API
     *
     * @param $destination
     * @param $text
     * @return array
     */
    private static function send($destination, $text)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.mysms.ph/api/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                'destination' => $destination,
                'text'        => $text
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Dc-Client-Id: ' . env('MYSMSPH_APP_ID'),
                'Dc-Client-Secret: ' . env('MYSMSPH_API_SECRET'),
                'Accept: application/json'
            )
        ));
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        if(isset($response['errors'])) {
            if(isset($response['errors']['to'])) {
                $response['errors']['mobile'] = $response['errors']['to'];
                unset($response['errors']['to']);
            }
        }

        return $response;
    }


    /****************************************************************************************************
     * Send One-Time Password (OTP)
     *
     * @param string $destination
     * @param string $otp
     * @param string $purpose
     * @return array
     */
    public static function otp($destination, $otp, $purpose = 'signing up')
    {
        $text = $otp . ' is your ' . env('APP_NAME') . ' verification code for ' . $purpose . '.';
        return self::send($destination, $text);
    }


    /****************************************************************************************************
     * Send One-Time Password (OTP) to a FAKE phone numbers:
     *
     * @param $destination
     * @param $otp
     * @return array
     */
    public static function otp_fake($destination, $otp) {
        $fake_mobiles = [
            '09000000000',
            '09111111111',
            '09222222222',
            '09333333333',
            '09444444444',
            '09555555555',
            '09666666666',
            '09777777777',
            '09888888888',
            '09999999999'
        ];

        if(in_array($destination, $fake_mobiles)) {
            return [
                'message' => 'Message successfully processed'
            ];
        }
        else {
            return [
                'message' => 'Only fake mobiles are allowed while on development.',
                'errors'  => [
                    'mobile' => ['Use 09000000000 to 09999999999']
                ]
            ];
        }
    }

}
