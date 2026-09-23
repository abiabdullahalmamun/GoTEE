<?php

namespace App\Services;

use App\Mail\SendEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SmsService
{
    protected BulkSmsService $bulkSmsService;
    protected ?string $url;

    public function __construct(BulkSmsService $bulkSmsService)
    {
        $this->url = config('services.sms.api_url');
        $this->bulkSmsService = $bulkSmsService;
    }


    public function sendSms(?string $mobile, string $message)
    {

        if($mobile == ''){
            return [
                'success' => false,
                'message' => 'Invalid Phone Number',
                'response' => null
            ];
        }

        if(strlen($mobile) == 11){
//                $payload = [
//                    'mobile' => $mobile,
//                    'sms'    => $message,
//                ];

                $users[] = [
                    'id'=>1,
                    'mobile' => $mobile,
                ];

                $this->bulkSmsService->sendBulkSms($users, $message);
//                $response = Http::withHeaders([
//                    'Content-Type' => 'application/json',
//                ])->withoutVerifying()->post($this->url, $payload);

                // Optionally handle errors
//                if ($response->failed()) {
//                    // You can log, throw exception or handle gracefully
//                    return [
//                        'success' => false,
//                        'message' => 'Failed to send SMS.',
//                        'response' => $response->body(),
//                    ];
//                }

//                return [
//                    'success' => true,
//                    'response' => $response->json(),
//                ];
        }
        else{
            return [
                'success' => false,
                'message' => 'Invalid Phone Number',
                'response' => null
            ];
        }
    }

    public function getMessageFormat($type,$value = '',$value2 = '',$value3 = '',$value4 = '') :string
    {
        $format = '';
        if($type == 'otp'){
            $format = 'Your verification code is '.$value.'. Please do not share this code with anyone.';
        }
        elseif($type == 'signup'){
            $format = 'Hi '.$value.', Your OTP is verification successful. within short time we will confirm your registration.';
        }
        elseif($type == 'signupAdmin'){
            $format = 'Hi '.$value.', Your account registration is successfully. within short time we will confirm your registration.';
        }
        elseif($type == 'signupConfirm'){
            $format = 'Welcome '.$value.'. sir, Your registration has been verified. Now you can take services from Mess. Now you can sign in '.$value2.' and your password. Thank you.';
        }
        elseif($type == 'booking'){
            $format = 'Your booking request has been received. booking id is '.$value2.'. After verify your request, you will get a confirmation message.';
        }
        elseif($type == 'bookingConfirm'){
            $format = 'Welcome '.$value.'. Your request '.$value2.' has been Approved. The room allocated to you is '.$value3.'. Thank you.';
        }
        elseif($type == 'bookingCancel'){
            $format = 'Hello '.$value.'. Your request '.$value2.' has been Cancel. Thank you.';
        }
        elseif($type == 'bill'){
            $format = $value.' Sir, Your Mess bill invoice is '.$value2.' and your total bill is BDT '.$value3.'/=  Bill detail copy has been send to your email also. Please Pay your bill within 7 days. Kind regards. Thank you.';
        }
        return $format;
    }


    public function sendEmailtoMember($email, $subject,$message){
        $userData = [
            'subject' => $subject,
            'email' => $email,
            'message' => str_replace('email','SMS',$message),
        ];

        Mail::to($email)->send(new SendEmail($userData));
    }
}
