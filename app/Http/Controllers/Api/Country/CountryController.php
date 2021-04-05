<?php

namespace App\Http\Controllers\Api\Country;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Models\CountryModel;
use Validator;

class CountryController extends Controller
{
    public function country() {
//        try{
//            $user = auth()->userOrFail();
//        }catch(\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e){
//            return response()->json(['error' => true, 'message' => $e->getMessage()], 401);
//        }
        return response()->json(['countries' => CountryModel::get()], 200);
    }

    public function countryById($id): \Illuminate\Http\JsonResponse
    {
        $country = CountryModel::find($id);
        if(is_null($country)){
            return response()->json(['error' => true, 'message' => 'Not found!'],404);
        }

        return response()->json($country,200);
    }

    public function addCountry(Request $req): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'alias' => 'required|min:2|max:2',
            'name' => 'required|min:3',
            'name_en' => 'required|min:3'
        ];

        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $country = CountryModel::create($req->all());

        return response()->json($country, 201);
    }

    public function editCountry(Request $req, $id): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'alias' => 'required|min:2|max:2',
            'name' => 'required|min:3',
            'name_en' => 'required|min:3'
        ];

        $validator = Validator::make($req->all(), $rules);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $country = CountryModel::find($id);
        if(is_null($country)){
            return response()->json(['error' => true, 'message' => 'Not found!'],404);
        }
        $country->update($req->all());
        return response()->json($country, 200);

    }

    public function deleteCountry(Request $req, $id): \Illuminate\Http\JsonResponse
    {
        $country = CountryModel::find($id);
        if(is_null($country)){
            return response()->json(['error' => true, 'message' => 'Not found!'],404);
        }
        $country->delete();
        return response()->json('', 200);
    }
}
