<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $data = Product::with('Category')->get();
        return response()->json([
            'message' => "Daftar Produk",
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'description' => 'string',
            'price' => 'required|numeric|min: 0',
            'category_id' => 'required|excists:categories_id'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'validator failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'data' => $product
        ], 201);
    }

    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        if(!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail Product',
            'data' => $product
        ]);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required|string',
            'description' => 'string',
            'price' => 'required|numeric|min: 0',
            'stock' => 'required|integer|min: 0',
            'category_id' => 'required|exists:categories_id'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($validator->validated());

        return response()->json([
            'message' => 'Produk berhasil diubah',
            'data' => $product
        ]);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if(!$product) {
            return response()->json([
                'message'=> 'Produk tidak ditemukan'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
