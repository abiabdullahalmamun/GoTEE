<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAptOverrideRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\Counter;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;
use App\Models\Service;
use App\Models\CurrentQueue;
use App\Models\QueueCode ; 
use App\Models\SslAptList ; 
use App\Models\SmsOtp ; 
use App\Models\SmsLog ; 
use App\Jobs\SendSmsJob;
use App\Jobs\SendSmsDCJob ; 
use App\Jobs\SendSmsDCAJob ; 
// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class  SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {
           $ips = request()->getClientIps(); 
            $lastHopIp = end($ips);

            $cenId = auth()->user()->centerId ; 
            $role = auth()->user()->role_id ;    
             // dd($role) ; 

             if($cenId){
                $cnt = Counter::select('center_id','counter_id','loginstate','tokenno','autoMan','visaType','stickerType')->where('ip',$lastHopIp )->first() ; 
                // dd($cnt) ; 
                if($cnt){
                     if($role== 5 ||  $role== 1 ||  $role== 13){
                        return view('pages.sms.index', [
                            'cenId' => $cenId,
                        ]);
                    }
                    else{
                        return view('pages.error.index', [
                            'error' => 'Counter not registered for this Center',
                        ]);
                    }

                }
                else{
                     return view('pages.error.index', [
                        'error' => 'Counter not registered ',
                    ]);
                }
            }
             else{
                 return view('pages.error.index', [
                        'error' => 'Page not permitted',
                    ]);
            }

     
        } catch (Exception $e) {
             dd($e->getMessage()); 
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function checkotp(Request $request)
    {
        $contact = $request->input('cont');
        $otp = $request->input('otpa');

        $exists = SmsOtp::select('id')->where('Date', date('Y-m-d'))->where('contact',$contact)->where('otp',$otp)->first() ; 

        if($exists){
                  return response()->json([
                    'found' => true,
                     'dd' =>  $otp ,
                ]);
        }
        else{
                  return response()->json([
                    'found' => false,
                     'dd' =>  $otp ,
                ]);
        }

   
    }
    public function store(Request $request)
    {
        // dd($request->all()) ; 
        $text = $request->input('smstext');
        $center = auth()->user()->centerId ; 
        $userId = auth()->user()->id ; 
        // dd($userId) ; 
        $randomNumber = rand(1000, 9999);
        // $text = 'Your OTP for submission is: '.$randomNumber.'    -IVAC' ; 
        try{

          if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');

            // Import and process file
            $rows = Excel::toArray([], $file)[0]; // Get first sheet
            $count = 0;
            foreach ($rows as $index => $row) {
                // Skip header if needed
                // if ($index === 0 && !is_numeric($row[0])) continue;
                if (empty($row[0])) continue;
                
                $Doit= SmsLog::create([
                        'Date'=> date('Y-m-d'),
                        'centerId'=> $center ,
                        'type'=>  0,
                        'contact'=> $row[0],
                        'text'=> $text,
                        'lang'=> 1,
                        'created_by'=> $userId,
                        'try'=> 0,
                        'created_at'=>Date('Y-m-d H:i:s'),
                        'updated_at'=>Date('Y-m-d H:i:s')
                    ]);
                if($Doit){
                    $recId = $Doit->id; 
                    $send = SendSmsDCJob::dispatch($row[0], $text, $recId );
                    $count++;

                }
               
            }

            return redirect()->route('send-sms.index')->with('success', " {$count} SMS Sent.");
        } 
        else {
            return redirect()->route('send-sms.index')->with('error', 'No Contact provided or file uploaded.');
        }


        } catch (Exception $e) {
            $mess = $e->getMessage(); 
           return response()->json([
                'found' => true,
                 'rep' => $mess ,
            ]);
        }
    }

    public function sendotp(Request $request)
    {
        $contact = $request->input('code');
        $center = auth()->user()->centerId ; 
        $randomNumber = rand(1000, 9999);
        $text = 'Your OTP for submission is: '.$randomNumber.'    -IVAC' ; 
        try{
            $send = SendSmsJob::dispatch($contact, $text );
            if($send){
                $Doit= SmsLog::create([
                    'Date'=> date('Y-m-d'),
                    'centerId'=> $center ,
                    'type'=>  0,
                    'contact'=> $contact,
                     'text'=> $text,
                      'lang'=> 1,
                    'created_at'=>Date('Y-m-d H:i:s'),
                    'updated_at'=>Date('Y-m-d H:i:s')
                ]);

             }
            $save= SmsOtp::create([
                'Date'=> date('Y-m-d'),
                'centerId'=> $center ,
                'contact'=> $contact,
                'otp'=> $randomNumber ,
                'created_at'=>Date('Y-m-d H:i:s'),
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);

            if($save){
                return response()->json([
                    'found' => true,
                     'rep' =>  $center ,
                ]);
            }
            else{
                 return response()->json([
                    'found' => false,
                    'rep' => 'no' ,
                ]);
            }

        } catch (Exception $e) {
            $mess = $e->getMessage(); 
           return response()->json([
                'found' => true,
                 'rep' => $mess ,
            ]);
        }
    }


}
