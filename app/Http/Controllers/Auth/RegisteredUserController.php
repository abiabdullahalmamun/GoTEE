<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Exception;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\UsersRegistrationRequest;
use App\Services\SmsService;

class RegisteredUserController extends Controller
{
    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $request->validate([
            'id_no' => 'required|string|unique:users,user_id',
            'rank' => 'required|integer|exists:user_ranks,id',
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'mobile_no' => ['required', 'regex:/^01[3-9][0-9]{8}$/'],
            'address' => ['nullable', 'string',],
            'password' => ['required', 'confirmed','min:4'],
        ],
        [
            'mobile_no.regex' => 'Mobile number must be 11 digits and start with 01XX',
        ]
        );



        $mobileNo = $request->mobile_no;
        $address = $request->address;
        $email = $request->email;

        $otp = strval(mt_rand(100000, 999999));

        try {
            if(User::where('email',$request->email)->exists()){
                return redirect()->route('website.signup')->with('error', 'This email is already registered with us.');
            }
            elseif(User::where('phone',$mobileNo)->exists()){
                return redirect()->route('website.signup')->with('error', 'This phone number is already exists');
            }

            $userRegReq = UsersRegistrationRequest::create([
                'details' => json_encode([
                    'id_number' => $request->id_no,
                    'buero_id'=> $request->buero_id,
                    'name' => $request->name,
                    'email' =>$email,
                    'rank' => $request->rank,
                    'phone' => $mobileNo,
                    'address' => $address,
                    'password' => Hash::make($request->password),
                ]),
                'is_active' => true,
                'otp' => $otp,
                'otp_verified_at' => null,
                'created_by' => auth()->user()->id ?? 0,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_by' => null,
            ]);
            if($userRegReq){
                $msg = $this->smsService->getMessageFormat('otp',$otp);
                $response = $this->smsService->sendSms(
                    $mobileNo,  // or $request->mobile if dynamic
                    $msg  // or dynamic message
                );
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $this->smsService->sendEmailtoMember($email, 'Registration OTP',$msg);
                }

                return redirect()->route('otp-verify', with(['id' => $userRegReq->id, 'phone' => $request->mobile_no]));

            }else{
                return back()->with('error', 'Something wrong');
            }

        } catch (Exception $e) {
            info('Error User Registration Request: ', [$e]);
//            return $e->getMessage();
            return back()->with('error', $e->getMessage());
        }
    }

    public function otpVerify(Request $request)
    {
        $request->validate([
            'id' => ['required', 'numeric', 'exists:users_registration_requests,id'],
            'phone' => ['required', 'string', 'exists:users_registration_requests,details->phone'],
        ]);


        $userRegReq = UsersRegistrationRequest::where('id', $request->id)
            ->where('details->phone', $request->phone)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$userRegReq) {
            return redirect()->route('website.signup')->with('error', 'Invalid Request.');
        }

        $diffInSeconds = now()->diffInSeconds($userRegReq->created_at);

        return view('frontend.verify-otp', with([
            'id' => $userRegReq->id,
            'mobile_no' => json_decode($userRegReq->details)->phone,
            'expiryTime' => 300, // seconds
            'created' => abs($diffInSeconds),
        ]));

    }

    public function update(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
            'id' => ['required', 'numeric', 'exists:users_registration_requests,id'],
            'mobile_no' => ['required', 'string', 'exists:users_registration_requests,details->phone'],
        ]);

        $userRegReq = UsersRegistrationRequest::where('id', $request->id)
            ->where('otp', $request->otp)
            ->where('is_active', true)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->orderByDesc('id')
            ->first();

        if (! $userRegReq) {
            info('Invalid Request');

            return back()->with('error', 'Invalid OTP.');
        }

        $details = json_decode($userRegReq->details);

        if ($details->phone !== $request->mobile_no) {
            info('Invalid Mobile Number');

            return back()->with('error', 'Invalid Mobile Number.');
        }

        if(User::where('phone',$details->phone)->exists()){
            return back()->with('error', 'This phone number is already exists');
        }

        if ($userRegReq->otp != $request->otp) {
            info('Invalid OTP');

            return back()->with('error', 'Invalid OTP.');
        }

        if ($userRegReq->otp_verified_at !== null) {
            info('OTP Already Verified');

            return back()->with('error', 'OTP Already Verified.');
        }

        if (now()->diffInMinutes($userRegReq->created_at) > 30) {
            info('OTP Expired');

            return back()->with('error', 'OTP Expired.');
        }

        try {
            DB::beginTransaction();
            $roleData = Role::where('is_access',0)->where('is_member',1)->first();

            if(!$roleData){
                return redirect()->route('otp-verify')->with('error', 'Member role not found');
            }


            $user = User::create([
                'role_id' => $roleData->id,
                'user_id' => $details->id_number,
                'buero_id'=> $details->buero_id,
                'name' => $details->name,
                'rank_id' => $details->rank,
                'email' => $details->email,
                'phone' => $details->phone,
                'password' => $details->password,
                'address' => $details->address,
                'is_active' => false,
                'is_approve' => false,
                'created_by' => 0,
            ]);

            $userRegReq->update([
                'is_active' => false,
                'otp_verified_at' => now(),
                'updated_by' => $user->id,
                'created_by' => $user->id,
                'updated_at' => now(),
            ]);

            $user->update([
                'created_by' => $user->id,
            ]);

            $msg = $this->smsService->getMessageFormat('signup',$details->name);
            $response = $this->smsService->sendSms(
                $details->phone,
                $msg
            );

            if(filter_var($details->email, FILTER_VALIDATE_EMAIL)){
                $this->smsService->sendEmailtoMember($details->email, 'OTP Verification',$msg);
            }


            DB::commit();

            return view('frontend.signup_confirmation',compact('details'));

        } catch (Exception $e) {
//            return $e->getMessage();
            DB::rollBack();
            info('Error OTP Verification:', [$e]);
            return redirect()->route('otp-verify')->with('error', 'Something went wrong while verifying OTP.');
        }
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'id' => ['required', 'numeric', 'exists:users_registration_requests,id'],
            'mobile_no' => ['required', 'string', 'exists:users_registration_requests,details->phone'],
        ]);

        $userRegReq = UsersRegistrationRequest::where('id', $request->id)
            ->where('details->phone', $request->mobile_no)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $userRegReq) {
            info('Invalid Request');

            return back()->with('error', 'Invalid OTP.');
        }

        $userRegReq->update([
            'otp' => strval(mt_rand(100000, 999999)),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'OTP Resend Successfully.');
    }
}
