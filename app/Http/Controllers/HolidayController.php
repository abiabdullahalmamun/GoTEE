<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;
use App\Models\Holiday;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $perPage = $request->input('per_page', 10);
        // $search = $request->input('search');

        try {

            $query = Holiday::query()->latest('Date');
            // dd($query) ; 
            // if ($search) {
            //     $query->where(function ($query) use ($search) {
            //         $fillableColumns = (new Floor)->getFillable();
            //         foreach ($fillableColumns as $column) {
            //             $query->orWhere($column, 'like', '%'.$search.'%');
            //         }
            //     });
            // }

            $query = $query->paginate(500);
            // dd($query) ; 
            return view('pages.holidays.index', with(['query' => $query]));

        } catch (Exception $e) {
            info('Error showing Floor!', [$e]);

            return redirect()->back()->with('error', 'holiday showing failed!.');
        }
    }

     
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHolidayRequest $request): RedirectResponse
    {
       // dd($request->all());
        try {
            $data = $request->validated();
            if($data['from_date']!=$data['to_date']){
                if($data['weekday']){
                    $startDate = \Carbon\Carbon::parse($data['from_date']);
                    $endDate   = \Carbon\Carbon::parse($data['to_date']);
                    $targetDay = strtolower($data['weekday']); 
                    
                    $daysToInsert = [];
                    while ($startDate->lte($endDate)) {
                        if (strtolower($startDate->format('l')) === $targetDay) {
                            $daysToInsert[] = [
                                'center_id'   => 1,
                                'Date'        => $startDate->toDateString(),
                                'description' => $data['description'],
                                'created_by'  => $data['created_by'],
                                'status'      => 1,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ];
                        }
                        $startDate->addDay();
                    }

                    // dd($daysToInsert) ;
                    if (!empty($daysToInsert)) {
                        // Holiday::upsert($sundays);
                         Holiday::upsert(
                            $daysToInsert,      // Data to insert/update
                            ['Date'],           // Unique key
                            ['description', 'created_by', 'status', 'updated_at'] // Columns to update if exists
                        );

                        return redirect()->route('holidays.index')
                            ->with('success', count($daysToInsert).' '.$data['weekday'].' inserted successfully.');
                    } else {
                        return redirect()->route('holidays.index')
                            ->with('error', 'No '.$data['weekday'].' found between '.$data['from_date'].' and '.$data['to_date']);
                    }
                }
                else{
                    return redirect()->route('holidays.index')
                            ->with('error', 'Please day of week...');
           
                }

            }
            else{
                 $save = Holiday::updateOrCreate(
                    ['Date' => $data['from_date']], // Match on Date
                    [
                        'center_id'   => 1,
                        'description' => $data['description'],
                        'created_by'  => $data['created_by'],
                        'status'      => 1,
                        'updated_at'  => now()
                    ]
                );
                if($save){
                    return redirect()->route('holidays.index')->with('success', 'Data Inserted successfully.');
                }
                else{
                     return redirect()->route('holidays.index')->with('error', 'Tag save failed!.');
                }
            }
        } catch (Exception $e) {
            dd($e) ; 
            return redirect()->route('holidays.index')->with('error', 'Data Insert failed!.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHolidayRequest $request, Holiday $holiday): RedirectResponse
    {
         dd($request->all());
        try {
            $data = $request->validated();
  // dd($data) ; 
            $shop->update($data);

            return redirect()->route('shops.index')->with('success', 'Floor updated successfully.');

        } catch (\Exception $e) {
          
            return redirect()->route('shops.index')->with('error', 'Floor updated failed!.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday): RedirectResponse
    {
         // dd($holiday->all());
        try {
            $holiday = Holiday::find($holiday->id); 
             if($holiday){
                $holiday->delete();
                return redirect()->route('holidays.index')->with('success', 'Data deleted successfully.');
             } 
             else{
                 return redirect()->route('holidays.index')->with('error', 'Data delete failed .');
             }

        } catch (\Exception $e) {
          
            return redirect()->route('holidays.index')->with('error', 'Shop deleted failed!.');
        }
    }
}
