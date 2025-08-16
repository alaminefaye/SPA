<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Purchase::with(['client', 'items']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($q) use ($search) {
                    $q->where('nom_complet', 'LIKE', "%{$search}%")
                      ->orWhere('numero_telephone', 'LIKE', "%{$search}%")
                      ->orWhere('adresse_mail', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('items', function($q) use ($search) {
                    $q->whereHas('product', function($subq) use ($search) {
                        $subq->where('name', 'LIKE', "%{$search}%");
                    });
                })
                ->orWhere('reference', 'LIKE', "%{$search}%")
                ->orWhere('quantity', 'LIKE', "%{$search}%")
                ->orWhere('total_amount', 'LIKE', "%{$search}%");
            });
        }
        
        $purchases = $query->latest()->paginate(10)->appends(['search' => $search]);
        
        return view('purchases.index', compact('purchases', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $clients = Client::all();
        $paymentMethods = ['cash', 'wave', 'orange_money'];
        
        return view('purchases.create', compact('products', 'clients', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données de base
        $rules = [
            'payment_method' => 'required|string|in:cash,wave,orange_money',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
        
        // Vérifier si c'est un client existant ou un nouveau client
        if ($request->client_id) {
            // Client existant
            $rules['client_id'] = 'exists:clients,id';
        } else if ($request->telephone) {
            // Nouveau client potentiel
            $rules['telephone'] = 'required|string';
            $rules['nom_complet'] = 'required|string|max:255';
            $rules['adresse_mail'] = 'nullable|email|max:255';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->route('purchases.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Gérer la création d'un nouveau client si nécessaire
            $clientId = $request->client_id;
            
            if (!$clientId && $request->telephone && $request->nom_complet) {
                // Vérifier si un client avec ce numéro de téléphone existe déjà
                $existingClient = Client::where('numero_telephone', 'LIKE', "%{$request->telephone}%")->first();
                
                if ($existingClient) {
                    $clientId = $existingClient->id;
                } else {
                    // Créer un nouveau client
                    $client = Client::create([
                        'nom_complet' => $request->nom_complet,
                        'numero_telephone' => $request->telephone,
                        'adresse_mail' => $request->adresse_mail ?? $request->telephone . '@example.com' // Email temporaire si non fournie
                    ]);
                    
                    $clientId = $client->id;
                }
            }
            
            // Préparer les données d'achat
            $purchaseData = [
                'client_id' => $clientId,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'status' => 'completed',
                'total_amount' => 0, // sera calculé par le modèle
            ];
            
            $purchase = Purchase::createWithItems($purchaseData, $request->items);
            
            DB::commit();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Achat enregistré avec succès.' . 
                    (!$request->client_id && $clientId ? ' Un nouveau client a été créé.' : ''));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('purchases.create')
                ->with('error', 'Erreur lors de l\'enregistrement de l\'achat: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['client', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Cancel a purchase and return products to stock
     */
    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return redirect()->route('purchases.show', $purchase)
                ->with('info', 'Cet achat est déjà annulé.');
        }
        
        DB::beginTransaction();
        
        try {
            // Return products to stock
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->increaseStock($item->quantity);
            }
            
            // Update purchase status
            $purchase->status = 'cancelled';
            $purchase->save();
            
            DB::commit();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Achat annulé avec succès et stock restauré.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Erreur lors de l\'annulation de l\'achat: ' . $e->getMessage());
        }
    }
    
    /**
     * Get product details via AJAX
     */
    public function getProductDetails(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
        
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'isLowStock' => $product->isLowStock(),
        ]);
    }
    
    /**
     * Imprimer le ticket d'achat
     */
    public function imprimerTicket(Purchase $purchase)
    {
        // Charger les relations nécessaires
        $purchase->load(['client', 'items.product']);
        
        // Calcul des points gagnés (si nécessaire)
        // Par exemple: 1 point pour chaque tranche de 10000 FCFA
        $pointsGagnes = floor($purchase->total_amount / 10000);
        
        return view('purchases.ticket', [
            'purchase' => $purchase,
            'pointsGagnes' => $pointsGagnes
        ]);
    }
}
