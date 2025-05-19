<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Enums\UserPermission;
use App\Models\ProductVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductVersionController extends Controller
{

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'destroy'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_documents)) {
                return response(null, 403);
            }
        });
    }

    public function store(Request $request, $project_id)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $version = new ProductVersion();
        $version->name = $request->name;
        $version->project_id = $project_id;
        $version->status = 1;
        $version->creator_id = Auth::id();

        $version->save();

        return ApiResponseClass::sendResponse($version->toArray(), "Product version successfully saved", 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
        ]);

        $version = ProductVersion::findOrFail($id);

        if ($version->status == 1) {
            $version->name = $request->name;

            $version->save();
            return ApiResponseClass::sendResponse($version->toArray(), "Product version successfully updated", 200);
        } else
            return ApiResponseClass::sendResponse($version->toArray(), "You can't change deactivated version", 403);
    }

    public function destroy(Request $request, $id)
    {
        $version = ProductVersion::findOrFail($id);

        if ($version->status == 0) {
            return ApiResponseClass::sendResponse($version->toArray(), "Product already deactivated", 403);
        }

        $version->status = 0;

        $version->save();
        return ApiResponseClass::sendResponse($version->toArray(), "Product version successfully deactivated", 200);
    }

}