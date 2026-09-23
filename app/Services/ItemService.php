<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Exceptions\ExceptionHandler;
use App\Http\Resources\Items\ItemResource;
use App\Models\Item;
use App\Models\StockItem;
use App\Repositories\ItemAttributeRepository;
use App\Repositories\HkProdCategoryRepository;
use App\Repositories\ItemImageRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemVariantRepository;
use App\Repositories\StoreRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    protected ItemRepository $itemRepo;
    protected ItemVariantRepository $itemVariantRepo;
    protected ItemAttributeRepository $itemAttributeRepo;

    protected ItemImageRepository $itemImageRepo;
    protected HkProdCategoryRepository $itemCateRepo;
    protected StoreRepository $storeRepository;

    public function __construct(ItemRepository $itemRepo, ItemVariantRepository $itemVariantRepo, ItemAttributeRepository $itemAttributeRepo, HkProdCategoryRepository $itemCateRepo, ItemImageRepository $itemImageRepo, StoreRepository $storeRepository)
    {
        $this->itemRepo = $itemRepo;
        $this->itemVariantRepo = $itemVariantRepo;
        $this->itemAttributeRepo = $itemAttributeRepo;
        $this->itemCateRepo = $itemCateRepo;
        $this->itemImageRepo = $itemImageRepo;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @throws ExceptionHandler
     */
    public function getAllItems() : AnonymousResourceCollection
    {
        try {

            $resp =  $this->itemRepo->getAll(true);
            return ItemResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    public function getAllDependancyHK() //: AnonymousResourceCollection
    {
        try {

            return $resp =  $this->itemCateRepo->getAll(true);
            return ItemResource::collection($resp);

        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function createItem(array $data) //: ?ItemResource
    {
        DB::beginTransaction();
        try {
            $authId = Auth::id();
            $createdAt = now();

            $itemData = [
                'name' => $data['name'],
                'item_code' => $this->generateItemCode($authId),
                'type_id' => $data['type_id'],
                'uom_id' => $data['uom_id'],
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'description' => $data['description'],
                'purchase_price' => $data['purchase_price'],
                'sell_price' => $data['sell_price'],
                'warranty_month' => $data['warranty_month'],
                'min_record' => $data['min_record'],
                'video_url' => $data['video_url'],
                'is_active' => $data['is_active'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'created_by' => $authId,
                'updated_by' => $authId,
            ];

//            dd($itemData);

            $createdItem = $this->itemRepo->create($itemData);
//
//            for($i = 0; $i < 500; $i++){
//                $val = [
//                    'name' => $data['name'].$i,
//                    'item_code' => $this->generateItemCode($authId).$i,
//                    'type_id' => $data['type_id'],
//                    'category_id' => $data['category_id'],
//                    'brand_id' => $data['brand_id'],
//                    'description' => $data['description'],
//                    'purchase_price' => $data['purchase_price'],
//                    'sell_price' => $data['sell_price'],
//                    'video_url' => $data['video_url'],
//                    'is_active' => $data['is_active'],
//                    'created_at' => $createdAt,
//                    'updated_at' => $createdAt,
//                    'created_by' => $authId,
//                    'updated_by' => $authId,
//                ];
//                Item::create($val);
//            }

            $defaultIndex = array_search(true, array_column($data['variants'], 'isDefault'));
            if ($defaultIndex === false) {
                $defaultIndex = 0;
            }

            $variantInsertedData = null;
            foreach ($data['variants'] as $key=>$variant) {
                $result = $this->getVariantFormate($variant,$createdItem->id,$authId,$createdAt);

                if($key == $defaultIndex) {
                    $variantInsertedData = $this->itemVariantRepo->create($result);
                }else{
                    $this->itemVariantRepo->create($result);
                }
            }

            if(isset($data['images']) && !empty($data['images'])) {
                $imagesData= $this->getImageFormat($data['images'],$createdItem->id);

                $this->itemImageRepo->create($imagesData);
            }

            $attributes = $this->getAttributeFormate($data['attributes'],$createdItem->id,$authId,$createdAt);

            $this->itemAttributeRepo->create($attributes);

            if($variantInsertedData){
                $createdItem->default_variant_id = $variantInsertedData->id;
                $createdItem->save();
            }

//            return $createdItem;
            DB::commit();
            return new ItemResource($createdItem);
        }
        catch(QueryException|Exception $e){
            DB::rollback();
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    protected function generateItemCode($authId): string
    {

        $itemData = $this->itemRepo->getLastItem($authId);
        if(!$itemData){
            $newItemNumber = 1;
        }else{
            $newItemNumber = $itemData->id + 1;
        }


        return 'ITM-' . str_pad($newItemNumber, 4, '0', STR_PAD_LEFT);

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
    protected function getImageFormat($imagesStr,$itemId): array
    {
        $imagesFormatData = [];

        foreach ($imagesStr as $imageStr) {

            $fileExtension = $this->getFileExtensionFromBase64($imageStr);
            // Generate a unique filename
            $fileName = 'itm_'.uniqid() . '.' . $fileExtension;

            // Decode the base64 string into binary data
            $fileData = $this->decodeBase64Image($imageStr);

            // Store the image in the public disk
            $filePath = Storage::disk('public')->put('images/items/' . $fileName, $fileData, 'public');

            $imagesFormatData[] =  [
                'item_id' => $itemId,
                'image_url' => '/storage/images/items/'.$fileName,
                'sequence' => 1,
                'is_active' => 1
            ];
        }
        return $imagesFormatData;
    }
    protected function getVariantFormate($variant,$itemId,$authId,$createdAt): array
    {
        return [
            'item_id' => $itemId,
            'name'=>$variant['name'],
            'value'=>$variant['value'],
            'regular_price'=>$variant['regular_price'],
            'sell_price'=>$variant['sell_price'],
            'init_stock_qty'=>$variant['init_stock_qty'],
            'created_by' => $authId,
            'created_at' => $createdAt,
        ];
    }
    protected function getAttributeFormate($attributes,$itemId,$authId,$createdAt): array
    {
        return array_map(function($attribute) use ($itemId,$authId,$createdAt) {
            return [
                'item_id' => $itemId,
                'name' => $attribute['name'],
                'description' => $attribute['description'],
                'created_by' => $authId,
                'created_at' => $createdAt,
            ];
        }, $attributes);
    }

    /**
     * @throws ExceptionHandler
     */
    public function getItemById(int $id): ?ItemResource
    {
        try {

            $findData = $this->itemRepo->getById($id);

            return new ItemResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function getItemLastStockPerStore(int $itemId) //: ?ItemWiseResource
    {
        try {
            $result = [];
            $storeList = $this->storeRepository->getAll(true);
            foreach ($storeList as $store) {
                $lastStock = StockItem::where('store_id', $store->id)->where('item_id', $itemId)->orderBy('id','DESC')->first();
                $lastStockQty = 0;
                if($lastStock){
                    $lastStockQty = doubleval($lastStock->cls_qty);
                }
                $result[] = [
                    'item_id' => $itemId,
                    'storeId' => $store->id,
                    'storeName' => $store->name,
                    'lastQty' => $lastStockQty,
                ];
            }
            return $result;
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function updateItem(int $id, array $data) : ?ItemResource
    {
        try {
            $authId = Auth::id();
            $createdAt = now()->format('Y-m-d H:i:s');
            $findData = $this->itemRepo->getById($id);
            $findData->name = $data['name'];
            $findData->type_id = $data['type_id'];
            $findData->uom_id = $data['uom_id'];
            $findData->category_id = $data['category_id'];
            $findData->brand_id = $data['brand_id'];
            $findData->description = $data['description'];
            $findData->video_url = $data['video_url'];
            $findData->purchase_price = $data['purchase_price'];
            $findData->sell_price = $data['sell_price'];
            $findData->warranty_month = $data['warranty_month'];
            $findData->min_record = $data['min_record'];
            $findData->is_active = $data['is_active'];
            $findData->updated_at = $createdAt;
            $findData->updated_by = $authId;

            $defaultIndex = array_search(true, array_column($data['variants'], 'isDefault'));
            if ($defaultIndex === false) {
                $defaultIndex = 0;
            }

            $variantInsertedData = null;
//            $this->itemVariantRepo->deleteByItemId($findData->id);
//            foreach ($data['variants'] as $key=>$variant) {
//                $result = $this->getVariantFormate($variant,$findData->id,$authId,$createdAt);
//                if($key == $defaultIndex) {
//                    $variantInsertedData = $this->itemVariantRepo->create($result);
//                }else{
//                    $this->itemVariantRepo->create($result);
//                }
//            }
//            if(isset($data['networkImages']) && !empty($data['networkImages'])) {
//                foreach($data['networkImages'] as $networkImageUrl) {
//                    $startPos = strpos($networkImageUrl, '/images');
//                    if ($startPos !== false) {
//                        $desiredPath = substr($networkImageUrl, $startPos);
//                        $this->itemImageRepo->deleteByUrl($findData->id,$desiredPath);
//                    }
//
//                }
//            }

            if(isset($data['images']) && !empty($data['images'])) {
                $this->itemImageRepo->deleteByItemId($findData->id);
                $imagesData= $this->getImageFormat($data['images'],$findData->id);
                $this->itemImageRepo->create($imagesData);
            }

//            $attributes = $this->getAttributeFormate($data['attributes'],$findData->id,$authId,$createdAt);
//            $this->itemAttributeRepo->deleteByItemId($findData->id);
//            $this->itemAttributeRepo->create($attributes);

//            if($variantInsertedData){
//                $findData->default_variant_id = $variantInsertedData->id;
//            }
            $findData->save();

            $findData = $this->itemRepo->getById($id);
            return ItemResource::make($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }

    /**
     * @throws ExceptionHandler
     */
    public function deleteItem(int $id): ItemResource
    {
        try {
            $this->itemImageRepo->deleteByItemId($id);
            $findData = $this->itemRepo->getById($id);
            $status = $findData->delete();
            if(!$status){
                throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR);
            }
            return new ItemResource($findData);
        }
        catch(QueryException $e){
            throw new ExceptionHandler(ResponseStatus::SERVER_ERROR,ResponseStatus::CODE_INTERNAL_SERVER_ERROR,$e);
        }
    }
}
