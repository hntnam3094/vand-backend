<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function create (Request $request) {
        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');
        $storeId = $request->get('storeId');
        $userId = $request->get('userId');
        $image =  $request->file('image');
        $imageUrl = '';

        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric|max:1200',
                'storeId' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 422);
        }

        if ($image) {
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $filename);
            $imageUrl = '/storage/images/' . $filename;
        }


        $result = Product::query()->updateOrInsert([
            'name' => $name,
            'description' => $description,
            'image' => $imageUrl,
            'price' => $price,
            'storeId' => $storeId,
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
            $query = Product::orderBy('created_at', 'desc');
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $result = $query->where('userId', $id)->paginate(8);
        }
        return response()->json(['data' => $result]);

    }

    public function getListByStoreId ($id, Request $request) {
        $result = [];
        if ($id) {
            $query = Product::orderBy('created_at', 'desc');
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            $result = $query->where('storeId', $id)->paginate(8);
        }
        return response()->json(['data' => $result]);

    }

    public function delete ($id) {
        if ($id) {
            $data = Product::query()->find($id);
            if ($data) {
                $result = Product::query()->where('id', $id)->delete();
                if ($result) {
                    return  response()->json(['message' => 'Success'], 200);
                }
            }
        }

        return  response()->json(['message' => 'Faild response'], 400);
    }

    public function show ($id) {
        if ($id) {
            $data = Product::query()->find($id);
            if ($data) {
                return  response()->json(['data' => $data], 200);
            }
        }
        return  response()->json(['message' => 'Faild response'], 400);
    }

    public function edit ($id, Request $request) {
        if ($id) {
            $oldData = Product::query()->find($id);
            if ($oldData) {
                $name = $request->get('name');
                $description = $request->get('description');
                $price = $request->get('price');
                $storeId = $request->get('storeId');
                $image =  $request->file('image');
                $imageUrl = '';
                $isNewImage = false;

                try {
                    $request->validate([
                        'name' => 'required',
                        'description' => 'required',
                        'price' => 'required|numeric|max:1200',
                        'storeId' => 'required'
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


                $result = Product::query()->where('id', $id)->update([
                    'name' => $name,
                    'description' => $description,
                    'image' => $imageUrl,
                    'price' => $price,
                    'storeId' => $storeId
                ]);

                if ($result) {
                    if ($isNewImage) {
                        $pathImage = explode('/', $oldData->image);
                        if ($pathImage && isset($pathImage[3])) {
                            Storage::delete('public/images/' . $pathImage[3]);
                        }
                    }

                    $dataNew = Product::query()->find($id);

                    return  response()->json(['data' => $dataNew], 200);
                }
            }

            return  response()->json(['message' => 'Faild response'], 400);
        }
    }
}
