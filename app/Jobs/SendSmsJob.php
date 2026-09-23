<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;


class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message )
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Replace with your SMS sending logic (e.g., API call)
        // Example using HTTP client:
        try{

         $data = array(
                   'contact' => $this->phone,
                    'text' => $this->message,
                     "user" => '2ra', 
                     "pass" => 'sMs@2024#2ra', 
                     // "contact" => $phone , 
                      // "text" =>'Your OTP for submission is: '.$randomNumber.'    -IVAC',
                      "center" =>'DHAKA (JFP)',
                );
             \Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withBody(json_encode($data), 'application/json')
              ->post('https://passtrack.net/passtrack/api/getSMS.php');

        }
        catch(\Exception $e){
              Log::error('SMS sending failed: ' . $e->getMessage());
            throw $e; // rethrow if you want it to retry or fail visibly
        }





        // Http::post('https://passtrack.net/passtrack/api/getSMS.php', [
        //     'contact' => $this->phone,
        //     'text' => $this->message,
        //      "user" => '2ra', 
        //      "pass" => 'sMs@2024#2ra', 
        //      // "contact" => $phone , 
        //       // "text" =>'Your OTP for submission is: '.$randomNumber.'    -IVAC',
        //       "center" =>'DHAKA (JFP)',

        // ]);

        // You could also use a service class like SmsService::send($this->phone, $this->message);
    }
}
