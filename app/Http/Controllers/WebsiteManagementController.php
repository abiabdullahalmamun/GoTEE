<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\FooterLink;
use App\Models\Gallery;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WebsiteManagementController extends Controller
{
    public function index(){

        return view('pages.website-management.create_update');
    }

    public function headerInfo()
    {
        try {
            $meta = CompanyInfo::select('title','company_name as comName','about_us as aboutUs','address','phone','email','logo_url as logoUrl', 'pdf_url','signature_url','signature_url2')->orderby('id', 'desc')->first();
            return response()->json([
                'success' => true,
                'result' => $meta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliders'
            ], 500);
        }
    }
    public function headerInfoStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'nullable|string|max:255',
                'companyName' => 'nullable|string|max:200',
                'aboutus' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:8192',
                'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'signature2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'pdf' => 'nullable|file|mimes:pdf|max:51200',
            ]);

            $imageUrl = null;
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('meta', 'public');
                $imageUrl = Storage::url($path);
            }

            $signatureUrl = null;
            if ($request->hasFile('signature')) {
                $path = $request->file('signature')->store('signature', 'public');
                $signatureUrl = Storage::url($path);
            }
            $signatureUrl2 = null;
            if ($request->hasFile('signature2')) {
                $path = $request->file('signature2')->store('signature', 'public');
                $signatureUrl2 = Storage::url($path);
            }

            $pdfUrl = null;
            if ($request->hasFile('pdf')) {
                $pathPDF = $request->file('pdf')->store('mess_policy_pdf', 'public');
                $pdfUrl = Storage::url($pathPDF);
            }

            $meta = CompanyInfo::latest()->first();

            if (!$meta) {
                $meta = CompanyInfo::create([
                    'title' => $request->title,
                    'company_name' => $request->companyName,
                    'about_us' => $request->aboutus,
                    'logo_url' => $imageUrl,
                    'signature_url' => $signatureUrl,
                    'signature_url2' => $signatureUrl2,
                    'pdf_url' => $pdfUrl,
                    'created_at' => now(),
                    'created_by' => Auth::id()
                ]);
            } else {
                $updateData = [
                    'title' => $request->title,
                    'company_name' => $request->companyName,
                    'about_us' => $request->aboutus,
                    'created_at' => now(),
                    'created_by' => Auth::id()
                ];

                if ($imageUrl) {
                    $updateData['logo_url'] = $imageUrl;
                }

                if ($pdfUrl) {
                    $updateData['pdf_url'] = $pdfUrl;
                }

                if ($signatureUrl) {
                    $updateData['signature_url'] = $signatureUrl;
                }
                if ($signatureUrl2) {
                    $updateData['signature_url2'] = $signatureUrl2;
                }

                $meta->update($updateData);
            }

            return response()->json([
                'success' => true,
                'data' => $meta,
                'message' => 'Company Info saved successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save company info: ' . $e->getMessage()
            ], 500);
        }
    }


    public function fetchSlider()
    {
        try {
            $sliders = Slider::select('id','image_url as imageUrl')->latest()->get();
            return response()->json([
                'success' => true,
                'result' => $sliders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliders'
            ], 500);
        }
    }

    public function sliderStore(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'text' => 'nullable|string|max:255'
            ]);

            // Store to correct location (storage/app/public/sliders)
            $path = $request->file('image')->store('sliders', 'public');
            // Generate correct URL
            $imageUrl = Storage::url($path); // Returns "/storage/sliders/filename.jpg"

            // Create slider
            $slider = Slider::create([
                'image_url' => $imageUrl,
                'text' => $request->text,
                'created_at' => now(),
                'created_by'=>Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $slider,
                'message' => 'Slider created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create slider: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sliderDestroy($id)
    {
        try {
            $slider = Slider::findOrFail($id);

            // Delete the image file
            $path = str_replace('/storage', 'public', $slider->image_url);
            Storage::delete($path);

            // Delete the record
            $slider->delete();

            return response()->json([
                'success' => true,
                'message' => 'Slider deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete slider: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchGallery()
    {
        try {
            $gallerys = Gallery::select('id','image_url as imageUrl')->latest()->get();
            return response()->json([
                'success' => true,
                'result' => $gallerys
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch gallerys'
            ], 500);
        }
    }

    public function galleryStore(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg',
                'text' => 'nullable|string|max:255'
            ]);

            $path = $request->file('image')->store('gallery', 'public');
            $imageUrl = Storage::url($path); // Returns "/storage/gallery/filename.jpg"

            // Create gallery
            $slider = Gallery::create([
                'image_url' => $imageUrl,
                'text' => $request->text,
                'created_at' => now(),
                'created_by'=>Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $slider,
                'message' => 'gallery created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create gallery: ' . $e->getMessage()
            ], 500);
        }
    }

    public function galleryDestroy($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);

            // Delete the image file
            $path = str_replace('/storage', 'public', $gallery->image_url);
            Storage::delete($path);

            // Delete the record
            $gallery->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gallery deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete gallery: ' . $e->getMessage()
            ], 500);
        }
    }


    public function footerInfo()
    {
        try {
            $meta = CompanyInfo::select('title','company_name as comName','about_us as aboutUs','address','phone','email','logo_url as logoUrl')->orderby('id', 'desc')->first();
            return response()->json([
                'success' => true,
                'result' => $meta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliders'
            ], 500);
        }
    }

    public function footerInfoStore(Request $request)
    {
        try {
            $request->validate([
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|string|max:50',
            ]);

            // Create meta
            $meta = CompanyInfo::orderby('id', 'desc')->first()->update([
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'updated_at' => now(),
                'updated_by'=>Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $meta,
                'message' => 'Meta created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create slider: ' . $e->getMessage()
            ], 500);
        }
    }


    public function footerLink()
    {
        try {
            $link = FooterLink::select('id','title as title','link_url as linkUrl')->latest()->get();
            return response()->json([
                'success' => true,
                'result' => $link
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliders'
            ], 500);
        }
    }


    public function footerLinkStore(Request $request){
        try {
            $request->validate([
                'title' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|string|max:50',
            ]);

            // Create meta
            $meta = FooterLink::create([
                'title' => $request->title,
                'link_url' => $request->url,
                'created_at' => now(),
                'created_by'=>Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $meta,
                'message' => 'Footer created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create slider: ' . $e->getMessage()
            ], 500);
        }
    }

    public function footerLinkDestroy(FooterLink $link)
    {
        $link->delete();
        return response()->json([
            'success' => true,
            'message' => 'Link deleted successfully'
        ]);
    }

    public function fetchService()
    {
        try {
            $sliders = Service::select('id','title as title','image_url as imageUrl','pdf_url as pdfUrl','ref_url as refUrl','description')->latest()->get();
            return response()->json([
                'success' => true,
                'result' => $sliders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sliders'
            ], 500);
        }
    }

    public function storeService(Request $request){
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg',
                'pdf' => 'nullable|file|mimes:pdf|max:2048',  // max:2048 means maximum 2MB file size
                'ref_url' => 'nullable|string|max:50',
            ]);

            $imageUrl = null;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('service', 'public');
                $imageUrl = Storage::url($path); // Returns "/storage/sliders/filename.jpg"
            }

            $pdfUrl = null;
            if ($request->hasFile('pdf')) {
                $pathPDF = $request->file('pdf')->store('service_pdf', 'public');
                $pdfUrl = Storage::url($pathPDF); // Returns "/storage/sliders/filename.jpg"
            }


            // Create service
            $meta = Service::create([
                'title' => $request->title,
                'image_url' => $imageUrl,
                'pdf_url' => $pdfUrl,
                'ref_url' => $request->ref_url,
                'created_at' => now(),
                'created_by'=>Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $meta,
                'message' => 'Service created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateService(Request $request, $serviceId){
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg',
                'pdf' => 'nullable|file|mimes:pdf|max:2048',  // max:2048 means maximum 2MB file size
                'ref_url' => 'nullable|string|max:50',
            ]);
            $service = Service::where('id', $serviceId)->firstOrFail();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('service', 'public');
                $imageUrl = Storage::url($path); // Returns "/storage/sliders/filename.jpg"
                $service->image_url = $imageUrl;
            }

            if ($request->hasFile('pdf')) {
                $pathPDF = $request->file('pdf')->store('service_pdf', 'public');
                $pdfUrl = Storage::url($pathPDF); // Returns "/storage/sliders/filename.jpg"
                $service->pdf_url = $pdfUrl;
            }


            $service->title = $request->title;
            $service->ref_url = $request->ref_url;
            $service->updated_at = now();
            $service->updated_by = Auth::id();
            $service->update();

            return response()->json([
                'success' => true,
                'data' => $service,
                'message' => 'Service updated successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to updated Service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function serviceLinkDestroy($serviceId){
        $service = Service::where('id', $serviceId)->firstOrFail();
        // Delete image
        Storage::delete(str_replace('/storage', 'public', $service->image_url));

        // Delete PDF if exists
        if ($service->pdf_url) {
            Storage::delete(str_replace('/storage', 'public', $service->pdf_url));
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }


}
