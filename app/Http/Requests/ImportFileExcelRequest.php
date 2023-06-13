<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportFileExcelRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'file' => 'required|mimes:xls,xlsx',
        ];
    }

}
