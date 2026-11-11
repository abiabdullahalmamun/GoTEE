<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationListRequest;
use App\Http\Requests\UpdateOperationListRequest;
use App\Models\Device;
 
use App\Models\Service;
use App\Models\DeviceSvc ; 
use App\Models\OrderNo ; 
use App\Models\OperationList;
use App\Models\Buyer ; 
use App\Models\BuyerItem ; 
use App\Models\MachineType;
use App\Models\OpnCodeBank;
use App\Models\ProcessGrp;

use Exception;
use Throwable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Http\JsonResponse;
use App\Services\ActionExceptionService;



class OperationListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {

            $query = OrderNo::where('status', 1)
                    ->latest('id')
                    ->get();
            $buyer = Buyer::where('status',1)->orderby('buyer','asc')->get(); 
           $item = BuyerItem::where('status',1)->orderby('itemName','asc')->get(); 

           $machineTypes = MachineType::where('status', 1)
                ->orderBy('typeName')
                ->get(['id', 'typeName']);

            $operationCodes = OpnCodeBank::where('status', 1)
                ->orderBy('OperationName')
                ->get(['OprNo', 'OperationName']);

            $processGroups = ProcessGrp::where('status', 1)
                ->orderBy('groupName')
                ->get(['id', 'groupName']);

            return view('pages.operationList.index', with(['query' => $query,'machineTypes' => $machineTypes, 'operationCodes' =>$operationCodes, 'processGroups' =>$processGroups, 'buyer' =>$buyer,  'itemList' =>$item]));

        } catch (Exception $e) {
            // info('Error showing Floor!', [$e]);
             $mess = $e->getMessage(); 
             dd($mess) ; 
            return redirect()->back()->with('error', 'OperationList showing failed!');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
 
public function store(StoreOperationListRequest $request): RedirectResponse
{
    // dd($request->all()) ; 
    try {
        $data = $request->validated();
        // dd($data) ; 
        $result = DB::transaction(function () use ($data) {

            $orderCode = $data['OrderCode'];
            $createdBy = $data['created_by'];

            // Submitted operation codes
            $submittedCodes = array_map(
                'strval',
                $data['OperationCode']
            );

            // Get existing operations for this order
            $existingOperations = OperationList::where('OrderCode', $orderCode)
                ->lockForUpdate()
                ->get();

            // Index existing records by OperationCode
            $existingByCode = $existingOperations->keyBy(
                fn ($item) => (string) $item->OperationCode
            );

            $inserted = 0;
            $updated = 0;

            foreach ($data['OperationCode'] as $i => $operationCode) {

                $operationCode = (string) $operationCode;
                $selectedLastOperation = (string) ($data['IsLastOperation'] ?? '');

                $isLast = ($operationCode === $selectedLastOperation) ? 'Y' : null;

                $operationData = [
                    'IsLastOperation' => $isLast,
                    'OprNo'            => $data['OperationSeqNo'][$i],
                    'OperationSeqNo'   => $data['OperationSeqNo'][$i],
                    'OperationCode'    => $operationCode,
                    'OperationName'    => $data['OperationName'][$i],
                    'MC_Type'          => $data['MC_Type'][$i],
                    'SMV'              => $data['SMV'][$i],
                    'Target'           => $data['Target'][$i],
                    'ManPower'         => $data['ManPower'][$i],
                    'grpCode'          => $data['grpCode'][$i],
                ];

                if ($existingByCode->has($operationCode)) {

                    $existingByCode[$operationCode]->update($operationData);
                    $updated++;

                } else {

                    OperationList::create(array_merge(
                        $operationData,
                        [
                            'OrderCode'  => $orderCode,
                            'created_by' => $createdBy,
                        ]
                    ));

                    $inserted++;
                }
            }
            // Find existing operations that were not submitted
            $toDrop = $existingOperations->whereNotIn(
                'OperationCode',
                $submittedCodes
            );

            $dropped = $toDrop->count();


            // Delete removed operations
            if ($dropped > 0) {
                OperationList::where('OrderCode',$orderCode)->whereIn('OperationCode', $toDrop->pluck('OperationCode'))
                    ->delete();
            }

           ActionExceptionService::store([
                'module'      => 'OperationListManagement',
                'action'      => 'Updated',
                'centerId'      => 1 ,
                'remarks'     =>$existingOperations, 
                'changeLog'      => $data ,
            ]);


            return [
                'inserted' => $inserted,
                'updated'  => $updated,
                'dropped'  => $dropped,
            ];
        });

        // dd($result) ; 
        return redirect()
            ->route('operationList.index')
            ->with('success', 'Operation list saved successfully!')
            ->with('sync_counts', $result);

    } catch (Throwable $e) {

        report($e);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Operation list save failed: ' . $e->getMessage());
    }
}

    /**
     * Update the specified resource in storage.
     */
      // public function update(Request $request ) 
    // public function update(UpdateOrderNoRequest $request, OrderNo $device): RedirectResponse
    // {
    //     // dd($request) ; 
    //      // dd($request->all());
    //     try {
    //         $data = $request->validated();

    //         $record = OrderNo::where('OrderCode', $data['OrderCode'])->first();
    //         if($record){
    //             $record->OrderNo = $data['OrderNo'];
    //              $record->attr = $data['attr'];
    //             $record->Style = $data['Style'];
    //             $record->buyer = $data['buyer'];
    //              $record->status = $data['status'];
    //             $record->totalsmv  = $data['totalsmv'];
    //             $record->item = $data['item'];
    //             $record->updated_at = now(); 
    //             $record->save(); // Fires 'updated' event → Loggable works

    //             return redirect()->route('orders.index')->with('success', 'Data Updated Successfully');
    //         }
    //         else{
    //             return redirect()->route('orders.index')->with('error', 'Failed to Update');
    //         }
       
    //     } catch (\Exception $e) {
    //           $mess = $e->getMessage(); 
    //         // dd($mess) ; 
    //         if (str_contains($mess, 'Duplicate entry')) {
    //              return redirect()->route('orders.index')->with('error', 'Device Already Exists');
    //         }
    //         else{
    //             return redirect()->route('orders.index')->with('error', 'Insert failed '.$mess);
    //         }
    //     }
    // }



    public function destroy(string $device): JsonResponse
    {
        try {

            $deleted = OperationList::where('OrderCode', $device)->delete();

            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No operations found for this order.',
                ], 404);
            }
            ActionExceptionService::store([
                'module'      => 'OperationListManagement',
                'action'      => 'Deleted',
                'centerId'      => 1 ,
                'remarks'     =>'OrderNo: '.$device, 
                'changeLog'      =>  '',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully removed {$deleted} operations for Order {$device}.",
                'deleted' => $deleted,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove operation list.',
            ], 500);
        }
    }

 
    public function loadOperations($orderCode)
    {
        $order = OrderNo::where('OrderCode', $orderCode)->firstOrFail();
        $operations = OperationList::with([
                        'mctype',
                        'operation',
                        'group'
                    ])
                    ->where('OrderCode', $orderCode)
                    ->orderBy('OperationSeqNo')
                    ->get();
 
        $groups = ProcessGrp::where('status', 1)
                ->orderBy('groupName')
                ->get(['id', 'groupName']);

        $machineTypes = MachineType::where('status', 1)
                ->orderBy('typeName')
                ->get(['id', 'typeName']);

        $operationCodes = OpnCodeBank::where('status', 1)
                ->orderBy('OperationName')
                ->get(['OprNo', 'OperationName']);

        $html = '';

        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:16px;">';

        $html .= '<h3 class="text-lg font-semibold text-purple-800 mb-0">';
        $html .= 'Order: ' . e($order->OrderNo);
        $html .= '</h3>';
 
        $html .= '<button type="button"
            id="addOperations"
            data-order-code="' . e($orderCode) . '"
            data-order-name="' . e($order->OrderNo) . '"
            data-operations=\'' . e($operations->toJson()) . '\'
            data-groups=\'' . e($groups->toJson()) . '\'
            data-machineTypes=\'' . e($machineTypes->toJson()) . '\'
            data-operationCodes=\'' . e($operationCodes->toJson()) . '\'
            style="background:#673AB7;color:white;padding:8px 16px;border-radius:5px;cursor:pointer;">
            + Add/Edit List
        </button>';

        $html .= '</div>';

        if ($operations->count()) {

            $html .= '<div class="overflow-x-auto">';
            $html .= '<table class="w-full text-sm border border-gray-300">';
            $html .= '<thead class="bg-purple-100"><tr>';

            $html .= '<th class="border p-2">SL</th>';
            $html .= '<th class="border p-2">Seq No</th>';
            $html .= '<th class="border p-2">Operation Code</th>';
            $html .= '<th class="border p-2">Operation Name</th>';
            $html .= '<th class="border p-2">Machine Type</th>';
            $html .= '<th class="border p-2">SMV</th>';
            $html .= '<th class="border p-2">Target</th>';
            $html .= '<th class="border p-2">ManPower</th>';
            $html .= '<th class="border p-2">Category</th>';
           
     
            $html .= '</tr></thead><tbody>';

            foreach ($operations as $index => $operation) {

                $html .= '<tr>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">' . ($index + 1) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->OperationSeqNo) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->operation?->OperationName ?? $operation->OperationName) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->OperationCode) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->mctype?->typeName ?? '') . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->SMV) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->Target) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->ManPower) . '</td>';

                $html .= '<td class="border p-2 ' .
    ($operation->IsLastOperation === 'Y' ? 'bg-yellow-100 font-semibold' : '') .
    '">'
                    . e($operation->group?->groupName ?? '') . '</td>';

 
            
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';

        } else {

            $html .= '<div class="text-center py-10 text-gray-500">';
            $html .= '<p>No OperationList found for this order.</p>';
            $html .= '</div>';

        }


        // Bottom action buttons - only when operations exist
        if ($operations->count()) {

            $html .= '<div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">';

            // Bottom left
            // $html .= '<button type="button"
            //     id="Delcarelast"
            //     data-order-code="' . e($orderCode) . '"
            //     style="background:#64B5F6; color:white; padding:8px 16px; border-radius:5px; cursor:pointer;">
            //     Declare Last process
            // </button>';

            // Bottom right
            $html .= '<button type="button"
                id="RemoveOperations"
                data-order-code="' . e($orderCode) . '"
                data-url="' . e(route('operationList.destroy', ['device' => $orderCode])) . '"
                style="background:#D32F2F; color:white; padding:8px 16px; border-radius:5px; cursor:pointer;">
                Remove List
            </button>';

            $html .= '</div>';
        }
 
        return response($html);
    }
}
