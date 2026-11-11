<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperatorRequest extends FormRequest
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
            'optyppe'     => 'required|integer|in:1,2,3,4,5,6,7',
            'emp_id'      => 'required|string|max:255',
            'emp_name'    => 'required|string|max:255',
            'designation' => 'required|integer|exists:tbl_emp_designation,id',
            'card_id'     => 'required|string|max:255',

            // Personal Information
            'grade'       => 'required|integer|exists:tbl_emp_grade,id',
            'gender1'     => 'required|integer|in:1,2,3',

            // Dates
            'dateofjoin'  => 'required|date',
            'evaluation'  => 'required|date',

            // Salary and Remarks
            'salary'      => 'nullable|numeric|min:0',
            'remarks'     => 'nullable|string|max:1000',

            // Status
            'status'      => 'required|in:0,1',
             'recId'      => 'required|numeric',

            // System Fields
            // 'created_by'  => 'required|integer',
            // 'created_at'  => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'optyppe.required' => 'Please select an Operator Type.',
            'optyppe.in' => 'Invalid Operator Type.',

            'emp_id.required' => 'The Operator ID is required.',
            'emp_name.required' => 'The Operator Name is required.',

            'designation.required' => 'Please select a Designation.',
            'card_id.required' => 'The Card ID is required.',

            'grade.required' => 'Please select a Grade.',
            'gender1.required' => 'Please select a Gender.',
            'gender1.in' => 'Invalid Gender selection.',

            'dateofjoin.required' => 'The Date of Join is required.',
            'evaluation.required' => 'The Date of Evaluation is required.',

            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
            'remarks.string' => 'Remarks must be a valid text.',

            'status.required' => 'Please select a Status.',
            'status.in' => 'The status must be either Active or Inactive.',

            // 'created_by.required' => 'The created_by field is required.',
            // 'created_at.required' => 'The created_at field is required.',
        ];
    }
}
