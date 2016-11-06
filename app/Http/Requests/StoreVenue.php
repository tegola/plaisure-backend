<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenue extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'aams_census_code'             => 'required',
            'aams_subject_enrollment_code' => 'required',
            'name'                         => 'required',
            'category_id'                  => 'required|integer',
            'latitude'                     => 'required|numeric|between:-90,90',
            'longitude'                    => 'required|numeric|between:-180,180'
        ];
    }
}
