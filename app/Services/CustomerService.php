<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Customers\CustomerResource;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerService
{
    protected CustomerRepository $customerRepo;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllCustomer() : AnonymousResourceCollection
    {
        try {

            $resp =  $this->customerRepo->getAll(true);
            return CustomerResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }



    public function getAllDependancyHK() //: AnonymousResourceCollection
    {
        try {

            return $resp =  $this->customerRepo->getAll(true);
            return CustomerResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createCustomer(array $data) : ?CustomerResource
    {
        DB::beginTransaction();
        try {
            $authId = Auth::id();
            $createdAt = now();

            $imagesUrl = $data['image'] ? $this->getImageFormat($data['image']) : null;

            $itemData = [
                'name' => $data['name'],
                'code' => $this->generateCode($authId),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'is_active' => $data['is_active'],
                'image_url' => $imagesUrl,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'created_by' => $authId,
                'updated_by' => $authId,
            ];
//            dd($itemData);
            $createdItem = $this->customerRepo->create($itemData);

            DB::commit();
            return new CustomerResource($createdItem);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function generateCode($authId): string
    {

        $itemData = $this->customerRepo->getLastCustomer($authId);
        if(!$itemData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $itemData->id + 1;
        }

        return 'SP-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);

    }
    private function getFileExtensionFromBase64($base64Image): string
    {
        // Match the mime type from the base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            return $matches[1]; // Return the file extension (jpeg, png, etc.)
        }
        return 'png'; // Default to 'png' if not found
    }

    private function decodeBase64Image($base64Image)
    {
        // Remove the base64 prefix if present
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        return base64_decode($imageData);
    }
    protected function getImageFormat($imageStr): string
    {

        $fileExtension = $this->getFileExtensionFromBase64($imageStr);
        // Generate a unique filename
        $fileName = 'splr_'.uniqid() . '.' . $fileExtension;

        // Decode the base64 string into binary data
        $fileData = $this->decodeBase64Image($imageStr);

        // Store the image in the public disk
        $filePath = Storage::disk('public')->put('images/customers/' . $fileName, $fileData, 'public');

        return '/storage/images/customers/'.$fileName;
    }


    /**
     * @throws ExceptionHandler
     */
    public function getCustomerById(int $id): ?CustomerResource
    {
        try {

            $findData = $this->customerRepo->getById($id);

            return new CustomerResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateCustomer(int $id, array $data) : ?CustomerResource
    {
        try {
            $authId = Auth::id();
            $createdAt = now()->format('Y-m-d H:i:s');
            $findData = $this->customerRepo->getById($id);
            $findData->name = $data['name'];
            $findData->phone = $data['phone'];
            $findData->address = $data['address'];
            $findData->is_active = $data['is_active'];
            $findData->updated_at = $createdAt;
            $findData->updated_by = $authId;

            if($data['image'] != ''){
                $findData->image_url = $data['image'] ? $this->getImageFormat($data['image']) : null;
            }
            $findData->save();

            $findData = $this->customerRepo->getById($id);
            return CustomerResource::make($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteCustomer(int $id): CustomerResource
    {
        try {
            $findData = $this->customerRepo->getById($id);
            $status = $findData->delete();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new CustomerResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
