<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateCollectionRequest extends FormRequest
{
    

    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'name' => 'required',
            'owner_email' => 'required|email',
        ];
    }
}
