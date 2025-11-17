<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderBrowserController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $orders = Order::with('customer', 'items.product')->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login as a customer to place order.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (! auth('web')->check()) {
            return redirect()->route('login')->with('success', 'Please login first.');
        }

        $request->validate([
            'items'          => ['required', 'array'],
            'items.*.qty'    => ['nullable', 'integer', 'min:0'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $rawItems = $request->input('items', []);
        $products = Product::whereIn('id', array_keys($rawItems))->get()->keyBy('id');

        $payloadItems = [];

        foreach ($rawItems as $productId => $item) {
            $qty = (int) ($item['qty'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            if (! $products->has($productId)) {
                continue;
            }

            $product = $products[$productId];

            $payloadItems[] = [
                'product_id' => $product->id,
                'variant_id' => null, // simple; variants later
                'qty'        => $qty,
                'price'      => (float) $product->base_price, // price server-side থেকে নেওয়া
            ];
        }

        if (empty($payloadItems)) {
            return back()->withErrors(['items' => 'Please set quantity for at least one product.']);
        }

        $data = [
            'discount' => (float) $request->input('discount', 0),
            'items'    => $payloadItems,
        ];

        $order = $this->orderService->create($data, auth('web')->user());

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order created successfully. Order #'.$order->order_number);
    }
}
