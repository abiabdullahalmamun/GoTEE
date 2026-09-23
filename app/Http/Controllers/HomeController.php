<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Bureau;
use App\Models\Slider;
use App\Models\Gallery;
use App\Models\Service;
use App\Models\RankType;
// use App\Models\RoomBook;
use App\Models\RoomType;
use App\Models\UserRank;
use App\Models\BillMaster;
use App\Models\CompanyInfo;
use App\Services\SmsService;
use Illuminate\Http\Request;
use NumberToWords\NumberToWords;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index(){

        $data['meta'] = CompanyInfo::select('title','company_name as comName','about_us as aboutUs','address','phone','email','logo_url as logoUrl')->orderby('id', 'desc')->first();
        // $data['sliders'] = Slider::select('id','image_url as imageUrl')->orderBy('id','ASC')->get();
        // $data['serviceList'] = Service::select('id','title as title','image_url as imageUrl','pdf_url as pdfUrl','ref_url as refUrl','description')->orderBy('id','ASC')->get();
        // $data['galleryList'] = Gallery::select('id','image_url as imageUrl')->orderBy('id','ASC')->limit(6)->get();
    
        return view('frontend.home',$data);
    }
    public function dashboard(){
        $userData = Auth::user();
        $roleData = Role::where('id',$userData->role_id)->where('is_access',0)->where('is_member',1)->first();
        if($roleData){
            return redirect()->route('website.index');
        }

        $role = auth()->user()->role_id ;   
        
        // $role=2 ;  
        if (in_array($role, [1,5,13])) {
            return view('dashboard');
        } else {
            return view('dashboardA');
        }

        
    }

    public function gallery(){
        $data['galleryList'] = Gallery::select('id','image_url as imageUrl')->latest()->get();
        return view('frontend.gallery',$data);
    }
    public function service($serviceId){
        $data['serviceInfo'] = Service::select('id','title as title','image_url as imageUrl','pdf_url as pdfUrl','ref_url as refUrl','description')->where('id',$serviceId)->first();

        return view('frontend.service_item',$data);
    }

    public function signin(){
        return view('frontend.signin');
    }

    public function signup(){
        $rankTypes = RankType::orderBy('name','asc')->get();
        $bureaus = Bureau::orderBy('name','asc')->get();
        $ranks = UserRank::where('status',1)->orderBy('name','asc')->get();
        return view('frontend.signup',compact('ranks', 'rankTypes', 'bureaus'));
    }

    // public function roomBooking(){
    //     if(!Auth::check()){
    //         return redirect()->route('website.signin');
    //     }
    //     $data['roomTypeList'] = RoomType::select('id','name as name')->where('status',1)->latest()->get();
    //     return view('frontend.room_booking',$data);
    // }

    // public function roomBookingList(){
    //     if(!Auth::check()){
    //         return redirect()->route('website.signin');
    //     }
    //     $userId = Auth::id();
    //     $data['bookingList'] = RoomBook::with(['room.roomType'])->where('user_id',$userId)->latest()->get();


    //     return view('frontend.room_booking_list',$data);
    // }



    public function profile(){
        if(!Auth::check()){
            return redirect()->route('website.signin');
        }
        return view('frontend.profile');
    }
    public function billList(){
        if(!Auth::check()){
            return redirect()->route('website.signin');
        }
        return view('frontend.bill_list');
    }

    public function billDetailByInvoice($invoiceNo){
        if(!Auth::check()){
            return redirect()->route('website.signin');
        }

        $bill = BillMaster::with(['items.billHead', 'user', 'booking.room.roomType'])->where('invoice_no',$invoiceNo)->where('user_id',Auth::id())->first();
        if(!$bill){
            return redirect()->route('website.billList');
        }
        $previousBills = BillMaster::with(['items.billHead', 'user', 'booking.room.roomType'])->where('merge_master_id',$bill->id)->get();

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');

        $amountInWords = ucfirst($numberTransformer->toWords($bill->total_payable));

        return view('frontend.bill_details',compact('bill','amountInWords','previousBills'));
    }

    public function billPaymentByInvoice($invoiceNo){
        if(!Auth::check()){
            return redirect()->route('website.signin');
        }


        $bill = BillMaster::with(['items.billHead', 'user', 'booking.room.roomType'])
            ->where('invoice_no',$invoiceNo)
            ->where('user_id',Auth::id())
            ->where('paid_status',0)
            ->first();

        if(!$bill){
            return redirect()->route('website.billList',$invoiceNo);
        }

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');

        $amountInWords = ucfirst($numberTransformer->toWords($bill->total_payable));

        return view('frontend.bill_payment',compact('bill','amountInWords'));
    }

    public function messPolicyPDF(){

        if(!Auth::check()){
            return redirect()->route('website.signin');
        }

        $messPolicyPdf = CompanyInfo::select('pdf_url as pdfUrl')->latest()->first();

        return view('frontend.mess_policy_pdf', compact('messPolicyPdf'));
    }


    public function getRank($rankTypeId): JsonResponse
    {
        $ranks = UserRank::where('rank_type_id', $rankTypeId)
                        ->orderBy('name','ASC')
                        ->get(['id','name']);

        return response()->json([
            'success' => true,
            'data'    => $ranks
        ]);
    }

}
