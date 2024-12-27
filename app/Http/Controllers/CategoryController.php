<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public function list()
    {
        try {
            $category = Category::with('products')->get();
            return $this->sendSuccess($category, "Categories get successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function get($id)
    {
        try {
            $category = Category::with('products')->find($id);

            if (!$category) {
                return $this->sendError("Category not found", 404);
            }

            return $this->sendSuccess($category, "Category get successfully.");
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
                'image' => 'required|string',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            if ($request->id) {
                $category = Category::find($request->id);
            } else {
                $category = new Category();
            }

            $category->name = $request->name;
            $category->description = $request->description;
            $category->image = $request->image;

            $category->save();

            return $this->sendSuccess($category, "Category details saved successfully.");
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

            $url = $this->upload('categories', 'image');
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
            
            $category = Category::find($id);

            if (!$category) {
                return $this->sendError("Category not found", 404);
            }

            $category->delete();
            return $this->sendSuccess([], "Category removed successfully.");

        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }
}
