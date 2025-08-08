<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = ProductCategory::withCount('products');
        
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }
        
        $categories = $query->paginate(10)->appends(['search' => $search]);
        
        return view('products.categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('product-categories.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        ProductCategory::create($validator->validated());
        
        return redirect()->route('product-categories.index')
            ->with('success', 'Catégorie de produit créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load('products');
        return view('products.categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return view('products.categories.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('product-categories.edit', $productCategory)
                ->withErrors($validator)
                ->withInput();
        }
        
        $productCategory->update($validator->validated());
        
        return redirect()->route('product-categories.index')
            ->with('success', 'Catégorie de produit mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Check if category has products
        if ($productCategory->products()->count() > 0) {
            return redirect()->route('product-categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
        }
        
        $productCategory->delete();
        
        return redirect()->route('product-categories.index')
            ->with('success', 'Catégorie de produit supprimée avec succès.');
    }
}
