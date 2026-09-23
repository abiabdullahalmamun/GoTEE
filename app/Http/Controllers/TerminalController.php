<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperatorRequest;
use App\Http\Requests\UpdateOperatorRequest;
use App\Models\Employee;
use App\Models\EmpShop;
use App\Models\Shop;
// use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        try {

            $query = Employee::query()->latest('id');

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $fillableColumns = (new Floor)->getFillable();
                    foreach ($fillableColumns as $column) {
                        $query->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            }

            $query = $query->paginate($perPage);

            return view('pages.operators.index', with(['query' => $query]));

        } catch (Exception $e) {
            info('Error showing operators!', [$e]);

            return redirect()->back()->with('error', 'operator showing failed!.');
        }
    }

            // Shop::create([
            //         'ShopName'=> $r->name,
            //         'Contact'=>$r->Contact,
            //         'message'=>$r->mess,
            //         'status'=>1,
            //         'created_by'=>Auth::user()->user_id,
            //         'created_at'=>Date('Y-m-d H:i:s'),
            //         'updated_at'=>Date('Y-m-d H:i:s')
            //     ]);
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperatorRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
            $data = $request->validated();
            // dd($data) ; 
          $rfid = dechex((int)$data['card_id']) ;
        // dd($rfid) ; 
        if(strlen($rfid)==7){
             $rfid = '0'.$rfid ; 
        }
         else if(strlen($rfid)==6){
             $rfid = '00'.$rfid ; 
        }
        else if(strlen($rfid)==5){
             $rfid = '000'.$rfid ; 
        }
          else if(strlen($rfid)==4){
             $rfid = '0000'.$rfid ; 
        }
          else if(strlen($rfid)==3){
             $rfid = '00000'.$rfid ; 
        }
          else if(strlen($rfid)==2){
             $rfid = '000000'.$rfid ; 
        }
          else if(strlen($rfid)==1){
             $rfid = '0000000'.$rfid ; 
        }
     
        $cc =str_split($rfid, 2);
        $dd = array_reverse($cc) ;
        $cardd = implode($dd) ;
        $card = strtoupper($cardd);

        if(strlen($card)==7){
            $card = $card.'0' ; 
        }
        else if(strlen($card)==6){
            $card = $card.'00' ; 
        }
        else if(strlen($card)==5){
            $card = $card.'000' ; 
        }
        else if(strlen($card)==4){
            $card = $card.'0000' ; 
        }
        else if(strlen($card)==3){
            $card = $card.'00000' ; 
        }
        else if(strlen($card)==2){
            $card = $card.'000000' ; 
        }

        $cardex = Employee::select('emp_id', 'emp_name')->where('card_id',$card)->where('status',1)->orderBy('id','desc')->first() ; 

        if($cardex){
            return redirect()->route('operators.index')->with('error', $card.' Already assigned to '.$cardex->emp_id.' ('.$cardex->emp_name.')');    
        }
        else{
             Employee::create([
                'company_id'=> 10005,
                'emp_id'     => $data['emp_id'],
                'emp_name'   => $data['emp_name'],
                'card_id'    =>   $card,               //$data['card_id'],
                'status'     => 1,
                'emp_type'   =>2, // assuming this is intentional
                'pin'        =>  $data['pin'], 

                'created_by'=> $data['created_by'],
                'created_at'=>Date('Y-m-d H:i:s'),
                'updated_at'=>Date('Y-m-d H:i:s')
            ]);


            return redirect()->route('operators.index')->with('success', 'Data Inserted successfully.');

        }
       
       
        } catch (Exception $e) {
             // dd($e); 
    
            info('Operator data insert  failed!', [$e]);

            return redirect()->route('operators.index')->with('error', 'Data Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperatorRequest $request, Employee $operator): RedirectResponse
    {
         // dd($request->all());
        try {
            $data = $request->validated();
            // dd($data) ; 
            $cardex = Employee::select('id','emp_id', 'emp_name')->where('card_id',$data['card_id'])->where('status',1)->orderBy('id','desc')->first() ; 

            if($cardex){
                if($cardex->id==$data['id']){
                   
                    $is_update = Employee::where('id',$data['id'])->update([
                        'emp_id'     => $data['emp_id'],
                        'emp_name'   => $data['emp_name'],
                        'card_id'    => $data['card_id'],               //$data['card_id'],
                        'pin'        =>  $data['pin'], 
                        'updated_at'=>Date('Y-m-d H:i:s')
                    ]);

                    if($is_update){
                        return redirect()->route('operators.index')->with('success',$data['emp_id'].' Updated Successfully');    
                    }
                } 
                else{
                    return redirect()->route('operators.index')->with('error',$data['card_id'].' Card Assigned to other Employee');    
                }
            }
            else{
                $rfid = dechex((int)$data['card_id']) ;
                // dd($rfid) ; 
                if(strlen($rfid)==7){
                     $rfid = '0'.$rfid ; 
                }
                 else if(strlen($rfid)==6){
                     $rfid = '00'.$rfid ; 
                }
                else if(strlen($rfid)==5){
                     $rfid = '000'.$rfid ; 
                }
                  else if(strlen($rfid)==4){
                     $rfid = '0000'.$rfid ; 
                }
                  else if(strlen($rfid)==3){
                     $rfid = '00000'.$rfid ; 
                }
                  else if(strlen($rfid)==2){
                     $rfid = '000000'.$rfid ; 
                }
                  else if(strlen($rfid)==1){
                     $rfid = '0000000'.$rfid ; 
                }
             
                $cc =str_split($rfid, 2);
                $dd = array_reverse($cc) ;
                $cardd = implode($dd) ;
                $card = strtoupper($cardd);

                if(strlen($card)==7){
                    $card = $card.'0' ; 
                }
                else if(strlen($card)==6){
                    $card = $card.'00' ; 
                }
                else if(strlen($card)==5){
                    $card = $card.'000' ; 
                }
                else if(strlen($card)==4){
                    $card = $card.'0000' ; 
                }
                else if(strlen($card)==3){
                    $card = $card.'00000' ; 
                }
                else if(strlen($card)==2){
                    $card = $card.'000000' ; 
                }
               // dd($card) ; 
                $cardex = Employee::select('id','emp_id', 'emp_name')->where('card_id',$card)->where('status',1)->orderBy('id','desc')->first() ; 
                 
                if($cardex){
                    return redirect()->route('operators.index')->with('error', $card.' Already assigned to '.$cardex->emp_id.' ('.$cardex->emp_name.')');    
                }
                else{
                    $is_update = Employee::where('id',$data['id'])->update([
                        'emp_id'     => $data['emp_id'],
                        'emp_name'   => $data['emp_name'],
                        'card_id'    => $card,               //$data['card_id'],
                        'pin'        =>  $data['pin'], 
                        'updated_at'=>Date('Y-m-d H:i:s')
                    ]);

                    if($is_update){
                        return redirect()->route('operators.index')->with('success',$data['emp_id'].' Updated Successfully');    
                    }
                    else{
                         return redirect()->route('operators.index')->with('error','Failed to  Update');   
                    }

                }
            }


            //  $is_update = Employee::where('sl',$updatedata->sl)->update([
            //         'stage' => 'QC',
            //         'machineID' => 'ForceUpdate',
            //         'lineDate' => Date('Y-m-d H:i:s'),
            //         'updated_at' => Date('Y-m-d H:i:s'),
            // ]);
            $operator->update($data);

            return redirect()->route('operators.index')->with('success', 'Floor updated successfully.');
// 
        } catch (\Exception $e) {
            $msss = $e->getMessage() ; 
            if (str_contains($msss, 'Duplicate entry')) {
                 return redirect()->route('operators.index')->with('error', 'Employee ID Duplicate!.');
            }
            else{
                dd($e->getMessage()); 
               info('Floor updated failed!', [$e]);
                return redirect()->route('operators.index')->with('error', 'Floor updated failed!.');

            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function OprShop(Employee $operator)
    {

         // dd($operator->id);
        try {
            $user = Employee::where('id',$operator->id)->first() ; 
            // $user = $emm->emp_id ; 
            // $userName = $emm->emp_name ; 
            // dd($userName) ; 
            $empShop = EmpShop::where('empId', $operator->id)->get() ; 
              // dd($empShop) ; 
            // $roles = Role::all();
            $shop = Shop::where('status',1)->latest()->get();
            return view('pages.operators.empShop', compact('empShop','shop', 'user'));


        } catch (\Exception $e) {
            info('Operator Shop assignement!', [$e]);

            return redirect()->route('operators.index')->with('error', 'operator assignement failed!.');
        }
    }


    public function updateAssign(Request $request)
    {
             // dd($request->all());
     try {   
        $emp = Employee::where('id', $request->eid)->first() ; 

        if($emp){
            // dd('yes') ; 
            // dd( $request->assigned_shops) ; 
            EmpShop::where('empId',$request->eid)->delete();
            $cn = count($request->assigned_shops) ; 
            for($i=0; $i<$cn; $i++){
                //  EmpShop::create([
                //     'empId'     => $request->eid,
                //     'shopId'   => $request->assigned_shops[$i],
                //     'created_at'=>Date('Y-m-d H:i:s'),
                //     'updated_at'=>Date('Y-m-d H:i:s')
                // ]);
                EmpShop::upsert(
                    [
                        [
                            'empId'      => $request->eid,
                            'shopId'     => $request->assigned_shops[$i],
                            'created_at' => Date('Y-m-d H:i:s'),
                            'updated_at' => Date('Y-m-d H:i:s')
                        ],
                    ],
                    ['empId', 'shopId'], // Unique keys to match
                    ['updated_at']       // Columns to update if exists
                );

            }
           
           return redirect()->route('operators.index')->with('success', $cn.' Shops updated successfully.');
         
           
        }
        else{
            return redirect()->route('operators.index')->with('error', 'Invalid Operator!.');
        }

      } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);
 dd($e->getMessage());
            return redirect()->route('operators.index')->with('error', 'operation failed!.');
        }
      
//
        // return redirect()->route('operators.index')->with('success', 'User updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $operator): RedirectResponse
    {
         // dd($ops->all());
        try {
            $operator->delete();

            return redirect()->route('operators.index')->with('success', 'operator deleted successfully.');

        } catch (\Exception $e) {
            info('Shop deleted failed!', [$e]);

            return redirect()->route('operators.index')->with('error', 'operator deleted failed!.');
        }
    }
}
