<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Product;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendGmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ProductController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function createProduct(Request $request)
    {
        try {
            $validateProduct = Validator::make($request->all(), [
                'userId' => 'required',
            ]);
            if ($validateProduct->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Validation error'),
                    'errors' => $validateProduct->errors()
                ], Response::HTTP_ACCEPTED);
            }
            $product = new Product();
            $product->user_id = $request->userId;
            $product->product_name = $request->productName;
            $product->price = $request->price;
            $product->created_at = now();
            $product->updated_at = now();
            $product->save();
            return response()->json([
                'status' => true,
                'message' => __('You have successfully created an product!'),
                'errors' => null,
                'data' => $product
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }
    public function editProduct(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => __('Validation error'),
                    'errors' => $validateUser->errors()
                ], Response::HTTP_ACCEPTED);
            }
            $user = Product::where('id', $request->id)->update([
                'user_id' => $request->userId,
                'product_name' => $request->productName,
                'price' => $request->price
            ]);
            return response()->json([
                'status' => true,
                'message' => __('You have successfully edit an product!'),
                'errors' => null,
                'data' => $user
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_ACCEPTED);
        }
    }

    public function deleteProduct($id)
    {
        try {
            if ($id == null) {
                return response()->json([
                    'status' => false,
                    'message' => __('User Id') . __('notexist'),
                    'errors' => null,
                    'data' => null
                ], Response::HTTP_ACCEPTED);
            }
            $deleted = Product::where('id', $id)->first();

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => __('Post') . __('notexist'),
                    'errors' => null,
                    'data' => null
                ], Response::HTTP_ACCEPTED);
            }
            $deleted = Product::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'message' => __('Success'),
                'errors' => null,
                'data' => $deleted
            ], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    
    public function getListProduct(Request $request)
    {
        $product = Product::all();
        return response()->json([
            'status' => true,
            'message' => __('Success'),
            'errors' => null,
            'data' => $product
        ], Response::HTTP_ACCEPTED);
    }

}
