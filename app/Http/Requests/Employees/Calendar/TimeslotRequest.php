<?php

namespace plunner\Http\Requests\Employees\Calendar;

use plunner\Http\Requests\Request;

class TimeslotRequest extends Request
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
            'time_start' => 'required|date_format:"Y-m-d H:i:s"',
            'time_end' => 'required|date_format:"Y-m-d H:i:s"',
        ];
    }
}
