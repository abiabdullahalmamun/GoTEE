<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAptOverrideRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Models\Center;
use App\Models\VisaType;
use App\Models\VisaTypeApt;
use App\Models\AptOverride;
use App\Models\StickerMap;

// use App\Imports\WebFileImport; 
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class GatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function checkApt($r){
       // dd($r) ; 

       try{
            $wf_no = $r['web'] ; 
    
            $serverIp = request()->server('SERVER_ADDR');

                // dd($serverIp);
            if($serverIp == "128.199.125.186"){
                // dd('here') ; 
                //rotiwala TEST           
               $url =  "https://123.200.19.116/issueTokenROUTE.php?web=".$wf_no ;
                $response = Http::withOptions([
                        'verify' => false,
                        'curl' => [
                            CURLOPT_SSL_VERIFYHOST => false,
                        ],
                        'http_errors' => false,
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                            'Expect' => '',
                        ],
                    ])->post($url);

                // dd($response->status(), $response->body());
                $status = $response->status();
                $body = $response->body();
                // Force decode JSON
                $data3 = json_decode($body, true);
                // dd($data3) ; 
                if($data3){
                    return $data3 ;
                }
                else{
                    return $response->body();;
                }

            }
            else{
                //IVAC DC

                $query = http_build_query([
                    "webFileNumber" => $wf_no
                ]);
               $url = 'https://pdp.ivacbd.com/api/v1/file/appointment/status?'.$query ; 
                // $response = Http::withOptions([
                //         'http_errors' => false,
                //         'headers' => [
                //             'Content-Type: application/x-www-form-urlencoded', 
                //             'Authorization: 35375202-51b2-463c-8be4-558f3e4df9bc',
                //             'User-Agent: IVAC-PDP-Service/1.0',
                //             'Expect'        => '',  
                //         ],
                //     ])
                //     ->post($url);

// $client = new \GuzzleHttp\Client();

// $res = $client->request('POST', $url, [
//     'headers' => [
//         'Authorization' => '35375202-51b2-463c-8be4-558f3e4df9bc',
//         'User-Agent' => 'IVAC-PDP-Service/1.0',
//     ],
// ]);

// $client = new \GuzzleHttp\Client();

// $response = $client->request('POST', $url, [
//     'headers' => [
//         'Authorization' => '35375202-51b2-463c-8be4-558f3e4df9bc',
//         'User-Agent' => 'IVAC-PDP-Service/1.0',
//         'Content-Type' => 'application/x-www-form-urlencoded',
//     ],
// ]);

$response = Http::withHeaders([
    'Authorization' => '35375202-51b2-463c-8be4-558f3e4df9bc',
    'User-Agent' => 'IVAC-PDP-Service/1.0',
])->send('POST', $url);

// dd($res->getBody()->getContents());
                // $response = Http::withOptions([
                //         'http_errors' => false,
                //     ])->withHeaders([
                //         'Content-Type' => 'application/x-www-form-urlencoded',
                //           'Authorization: 35375202-51b2-463c-8be4-558f3e4df9bc',
                //         'User-Agent' => 'IVAC-PDP-Service/1.0',
                //         'Expect' => '',
                //     ])->post($url);

                // dd($response->status(), $response->body());
                $status = $response->status();
                $body = $response->body();
                // Force decode JSON
                $data3 = json_decode($body, true);
                // dd($data3) ; 
                // return $data3 ;
                if($data3){
                    return $data3 ;
                }
                else{
                    return $response->body();;
                }
            }


        
       } 
       catch(Exception $e){
            // return $data ;
            dd($e->getMessage()); 
       }


    }

}



// curl --location --request POST 'https://api.ivacbd.com/iams/api/v1/file/appointment/status?webFileNumber=BGDDV0FCF526' \
// --header 'Authorization: 35375202-51b2-463c-8be4-558f3e4df9bc' \
// --data ''



