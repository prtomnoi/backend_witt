<?php

namespace App\Services;

use App\Models\Amphur;
use App\Models\District;
use App\Models\Province;
use App\Models\Zipcode;

class AddressService
{

    public function getAllProvinces()
    {
        return Province::all();
    }

    public function getAmphursByProvinceId($provinceId)
    {
        return Amphur::where('province_id', $provinceId)->get();
    }

    public function getDistrictsByAmphurId($amphurId)
    {
        return District::where('amphur_id', $amphurId)->get();
    }

    public function getZipcodeByDistrictCode($districtCode)
    {
        return Zipcode::where('district_code', $districtCode)->first();
    }
}
