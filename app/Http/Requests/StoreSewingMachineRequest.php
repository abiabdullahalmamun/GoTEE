<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSewingMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }



    public function rules(): array
    {
        return [
            // Machine Information
            'machineType'  => 'required|integer|exists:tbl_machine_type,id',
            'machineBrand' => 'required|integer|exists:tbl_machine_brand,id',
            'machineId' => 'required|string|max:100|unique:tbl_machine,machine_id',
            // 'machineId'    => 'required|string|max:100',
            'machineModel' => 'required|string|max:150',
            'description'  => 'nullable|string|max:255',

            // Rental Information
            'rent'          => 'required|in:0,1',
            'rentref'       => 'nullable|string|max:255',

            // Status
            'status'        => 'required|in:0,1',

            // System Fields
            'created_by'    => 'required|integer',
            'created_at'    => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'machineType.required'  => 'Please select a Machine Type.',
            'machineType.exists'    => 'Invalid Machine Type selected.',

            'machineBrand.required' => 'Please select a Machine Brand.',
            'machineBrand.exists'   => 'Invalid Machine Brand selected.',
            'machineId.unique' => 'This Machine ID already exists. Please enter a different Machine ID.',
            'machineId.required'    => 'Please enter the Machine ID.',
            'machineModel.required' => 'Please enter the Machine Model.',
            // 'description.required'  => 'Please enter the Machine Description.',

            'rent.required'         => 'Please select OWN or RENTED.',
            'rent.in'               => 'Invalid rental selection.',

            // 'rentref.required'      => 'Please enter the Rent Reference.',
            'status.required'       => 'Please select Machine Status.',
            'status.in'             => 'Invalid Machine Status.',
        ];
    }



}
