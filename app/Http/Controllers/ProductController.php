<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function list()
    {
        try {
            $product = Product::with('category')->get();
            return $this->sendSuccess($product, "Products get successfully.");
        } catch (\Throwable $th) {
            return $th->getMessage();
            return $this->sendError("Server Error", 500);
        }
    }

    public function get($id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product) {
                return $this->sendError("Product not found", 404);
            }

            return $this->sendSuccess($product, "Product get successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function save(Request $request)
    {
        try {
            // check is admin
            $user = Auth::user();
            if (!$user->is_admin) {
                return $this->sendError("Unauthorized", 401);
            }

            // validation
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required|min:150',
                'image' => 'required|string',
                'price' => 'required|numeric',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            if ($request->id) {
                $product = Product::find($request->id);
            } else {
                $product = new Product();
                $product->category_id = $request->category_id;
            }

            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->image = $request->image;
            $product->is_hot = $request->is_hot;
            $product->is_active = $request->is_active;

            if (is_string($request->ingredients)) {
                $product->ingredients = explode(',', $request->ingredients);
            } else {
                $product->ingredients = $request->ingredients;
            }

            if ($request->stock) {
                $product->stock = $request->stock;
            }

            $product->save();

            return $this->sendSuccess($product->load('category'), "Product details saved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function media(Request $request)
    {
        try {
            // check is admin
            $user = Auth::user();
            if (!$user->is_admin) {
                return $this->sendError("Unauthorized", 401);
            }

            // validation
            $validation = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5096',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $url = $this->upload('products', 'image');
            return $this->sendSuccess(['url' => $url], "Media uploaded successfully.");

        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function delete($id)
    {
        try {
            // check is admin
            $user = Auth::user();
            if (!$user->is_admin) {
                return $this->sendError("Unauthorized", 401);
            }

            $product = Product::find($id);

            if (!$product) {
                return $this->sendError("Product not found", 404);
            }

            $product->delete();
            return $this->sendSuccess([], "Product removed successfully.");

        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }
}
