<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class StoreController extends Controller
{
    public function create (Request $request) {
        $name = $request->get('name');
        $description = $request->get('description');
        $address = $request->get('address');
        $phoneNumber = $request->get('phoneNumber');
        $userId = $request->get('userId');
        $image =  $request->file('image');
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'address' => 'required',
                'phoneNumber' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 422);
        }

        $imageUrl = '';
        if ($image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $filename);
            $imageUrl = '/storage/images/' . $filename;
        }


        $result = Store::query()->updateOrInsert([
            'name' => $name,
            'description' => $description,
            'image' => $imageUrl,
            'address' => $address,
            'phoneNumber' => $phoneNumber,
            'userId' => $userId
        ]);

        if ($result) {
            return  response()->json(['message' => 'Success'], 200);
        }

        return  response()->json(['message' => 'Faild response'], 400);
    }

    public function getList ($id, Request $request) {
        $result = [];
        if ($id) {
            $query = Store::orderBy('created_at', 'desc');
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $result = $query->where('userId', $id)->paginate(8);
        }
        return response()->json(['data' => $result]);
    }

    public function getListAll ($id, Request $request) {
        $result = [];
        if ($id) {
            $result = Store::query()->where('userId', $id)->orderBy('created_at', 'desc')->get();
        }
        return response()->json(['data' => $result]);
    }

    public function delete ($id) {
        if ($id) {
            $data = Store::query()->find($id);
            if ($data) {
                $result = Store::query()->where('id', $id)->delete();
                if ($result) {
                    return  response()->json(['message' => 'Success'], 200);
                }
            }
        }

        return  response()->json(['message' => 'Faild response'], 400);
    }

    public function show ($id) {
        if ($id) {
            $data = Store::query()->find($id);
            if ($data) {
                return  response()->json($data, 200);
            }
        }
        return  response()->json(['message' => 'Faild response'], 400);
    }

    public function edit ($id, Request $request) {
        if ($id) {
            $oldData = Store::query()->find($id);
            if ($oldData) {
                $name = $request->get('name');
                $description = $request->get('description');
                $address = $request->get('address');
                $phoneNumber = $request->get('phoneNumber');
                $userId = $request->get('userId');
                $image =  $request->file('image');
                $imageUrl = '';
                $isNewImage = false;
                try {
                    $request->validate([
                        'name' => 'required',
                        'description' => 'required',
                        'address' => 'required',
                        'phoneNumber' => 'required'
                    ]);
                } catch (ValidationException $e) {
                    return response()->json($e->errors(), 422);
                }

                if ($image) {
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/images', $filename);
                    $imageUrl = '/storage/images/' . $filename;
                    $isNewImage = true;
                } else {
                    $imageUrl = $oldData->image;
                }


                $result = Store::query()->where('id', $id)->update([
                    'name' => $name,
                    'description' => $description,
                    'image' => $imageUrl,
                    'address' => $address,
                    'phoneNumber' => $phoneNumber,
                    'userId' => $userId
                ]);

                if ($result) {
                    if ($isNewImage) {
                        $pathImage = explode('/', $oldData->image);
                        if ($pathImage && isset($pathImage[3])) {
                            Storage::delete('public/images/' . $pathImage[3]);
                        }
                    }

                    $dataNew = Store::query()->find($id);

                    return  response()->json(['data'=>$dataNew], 200);
                }
            }

            return  response()->json(['message' => 'Faild response'], 400);
        }
    }
}
