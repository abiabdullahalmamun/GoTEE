<?php
namespace App\Http\Requests\Api\Issues;


use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IssueIdRequest extends FormRequest{

    public function authorize() : bool
    {
        return true;
    }

    public function all($keys = null): array
    {
        $data['id'] = $this->route('issueId');
        return $data;
    }

    public function rules() : array
    {
        return [
            'id'=>[
                'required',
                'integer',
                Rule::exists('issues', 'id')->where(fn ($query) => $query->where('trans_type_id', 3)),
            ],

        ];
    }

    public function messages() : array
    {
        return [
            'id.exists'=>'Issue not found'
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        ApiResponse::validationThrowException($validator);
    }

}
