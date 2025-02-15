<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OfferController extends BaseController
{
    public function list()
    {
        try {
            $offer = Offer::where('is_active', 1)->get();
            return $this->sendSuccess($offer, "Offeres get successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function get($id = null)
    {
        try {
            $offer = Offer::with('products')->find($id);

            if (!$offer) {
                $products = Product::where('is_offer', 1)->orderBy('created_at', 'desc')->get();
                return $this->sendSuccess($products, "Offeres get successfully.");
            }

            return $this->sendSuccess($offer, "Offer get successfully.");
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
                'image' => 'required|string',
                'product_ids' => 'required|array',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $offer = Offer::findOrNew($request->id);
            $offer->banner = $request->image;
            $offer->is_active = (bool) $request->is_active;
            $offer->save();

            $offer->products()->sync($request->product_ids);

            return $this->sendSuccess($offer, "Offer details saved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
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

            $url = $this->upload('offer_banner', 'image');
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

            $offer = Offer::find($id);

            if (!$offer) {
                return $this->sendError("Offer not found", 404);
            }

            $offer->delete();
            return $this->sendSuccess([], "Offer removed successfully.");

        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }
}
