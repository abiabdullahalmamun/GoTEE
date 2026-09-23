<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BulkSmsService;
use Illuminate\Http\Request;

class BulkSmsController extends Controller
{
    protected BulkSmsService $smsService;

    public function __construct(BulkSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function create(){

        return view('pages.bulksms.create');
    }

    public function ActiveMembers(){
        $users = User::whereHas('role', function ($query) {
            $query->where('is_access', 0);
            $query->where('is_member', 1);
        })->with(['bureau','rank'])
            ->orderBy('id','DESC')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bueroName' => $user->bureau?->name ?? null,
                    'rankName' => $user->rank?->name ?? null,
                    'phone' => $user->phone,
                ];
            });

        return response()->json($users);
    }

    public function sendBulkSms(Request $request){
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'message' => 'required|string|max:160'
        ]);

        // Get users with their phone numbers
        $users = User::whereIn('id', $request->user_ids)
            ->get(['id', 'phone as mobile'])
            ->toArray();

        // Dispatch bulk SMS
        $this->smsService->sendBulkSms($users, $request->message);

        return response()->json([
            'success' => true,
            'message' => 'SMS are being sent in the background',
            'total_recipients' => count($users)
        ]);


    }



}
