<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UrlBrand;
use App\Models\UrlConfig;
use Illuminate\Validation\Rule;


class UrlBrandController extends Controller
{
    public function index()
    {
        $url_brand = UrlBrand::latest()->get();
        return view('url_brand.index', compact('url_brand'));
    }

    public function create()
    {
        return view('url_brand.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:url_brands,name', 
        ]);
        $input['name'] = $request->name;
        $UrlBrand = UrlBrand::create($input);
        return redirect()->route('url_brand.index')->with('success', 'UrlBrand created successfully.');
    }

    public function edit(UrlBrand $UrlBrand)
    {
        return view('url_brand.edit', compact('UrlBrand'));
    }

    public function update(Request $request, UrlBrand $UrlBrand)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('url_brands', 'name')->ignore($UrlBrand->id),
            ],
        ]);
        $input['name'] = $request->name;
        $UrlBrand->update($input);
        return redirect()->route('url_brand.index')->with('success', 'UrlBrand updated successfully.');
    }

    public function destroy(UrlBrand $UrlBrand)
    {
        $UrlConfig = UrlConfig::where('url_brand_id',$UrlBrand->id)->delete();
        $UrlBrand->delete();
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }

}

