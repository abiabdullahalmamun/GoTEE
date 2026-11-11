<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSewingMachineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $rules = $this->rules();
        $fields = array_keys($rules);

        // $this->merge([
        //     'updated_by' => auth()->user()->id,
        //     'updated_at' => \Carbon\Carbon::now(),
        // ]);
    }

    public function rules()
    {
        return [
            'machineType'  => 'required|integer|exists:tbl_machine_type,id',
            'machineBrand' => 'required|integer|exists:tbl_machine_brand,id',
            // 'machineId' => 'required|string|max:100',
            'machineId' => [
                            'required',
                            'string',
                            'max:100',
                            Rule::unique('tbl_machine', 'machine_id')
                                ->ignore($this->input('recId'), 'id'),
                        ],
            // 'machineId'    => 'required|string|max:100',
            'machineModel' => 'required|string|max:150',
            'description'  => 'nullable|string|max:255',

            // Rental Information
            'rent'          => 'required|in:0,1',
            'rentref'       => 'nullable|string|max:255',

            // Status
            'status'        => 'required|in:0,1',
             // 'recId'      => 'required|numeric',
         'recId' => 'required|integer|exists:tbl_machine,id',
        ];
    }

    public function messages()
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
