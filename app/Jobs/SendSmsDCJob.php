<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\SmsLog ; 

class SendSmsDCJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $recordId;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message, $recordId )
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->recordId = $recordId ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Replace with your SMS sending logic (e.g., API call)
        // Example using HTTP client:
        try{

                $data = [
                    'contact' => $this->phone,
                    'text'    => $this->message,
                    'user'    => '2ra',
                    'pass'    => 'sMs@2024#2ra',
                    'center'  => 'DHAKA (JFP)',
                ];

                $response = \Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->withBody(json_encode($data), 'application/json')
                  ->post('https://passtrack.net/passtrack/api/getSMS.php');

                  
                // Check response
                if ($response->successful()) {
                    $reply = $response->json(); // decode JSON
                    if( $reply['status']==200){
                        $message = $reply['message'] ?? null;
                        // dd($message) ; 
                        $is_update = SmsLog::where('id', $this->recordId)->update([
                            'txn'       =>   $message, 
                            'updated_at'=>Date('Y-m-d H:i:s')
                        ]);

                    }
                    
                    \Log::info('SMS sent', ['phone' => $this->phone, 'reply' => $reply]);
                } else {
                    \Log::error('SMS API failed', ['status' => $response->status(), 'body' => $response->body()]);
                }

// array:3 [
//   "status" => "200"
//   "reference" => "20251002-6270-310028447997"
//   "message" => "20251002-6270-310028447997-01708404440-02"
// ] 


        }
        catch(\Exception $e){
              Log::error('SMS sending failed: ' . $e->getMessage());
            throw $e; // rethrow if you want it to retry or fail visibly
        }

    }
}
