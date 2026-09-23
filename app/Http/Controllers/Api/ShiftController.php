<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shifts\ShiftIdRequest;
use App\Http\Requests\Api\Shifts\CreateShiftRequest;
use App\Http\Requests\Api\Shifts\UpdateShiftRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ShiftService;
use Exception;
use Illuminate\Http\JsonResponse;


class ShiftController extends Controller
{

    protected ShiftService $shiftService;

    public function __construct(ShiftService $shiftService){
        $this->shiftService = $shiftService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $resp = $this->shiftService->getAllShifts();

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateShiftRequest $request): JsonResponse
    {
        try {
            $resp = $this->shiftService->createShift($request->validated());

            return ApiResponse::success($resp, 'Shift inserted successful',ResponseStatus::CODE_CREATED);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftIdRequest $shiftId): JsonResponse
    {
        try {

            $resp = $this->shiftService->getShiftById($shiftId->id);

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, ShiftIdRequest $shiftId): JsonResponse
    {
        try {

            $resp = $this->shiftService->updateShift($shiftId->id,$request->validated());

            return ApiResponse::success($resp, ResponseStatus::SUCCESS);

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftIdRequest $shiftId): JsonResponse
    {
        try {

            $resp = $this->shiftService->deleteShift($shiftId->id);

            return ApiResponse::success($resp,'Shift removed successfully');

        } catch (ExceptionHandler $e) {
            return ApiResponse::ExceptionHandlerResponse($e);
        }
        catch (Exception $e) {
            return ApiResponse::ServerError($e);
        }
    }

}
