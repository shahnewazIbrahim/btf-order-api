<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function index(Request $request)
    {
        $user = auth('api')->user();

        $query = Order::with('customer', 'items.product', 'items.variant')
            ->orderByDesc('id');

        if ($user) {
            $role = $user->role->name ?? null;

            if ($role === 'Customer') {
                // শুধু নিজের অর্ডার
                $query->where('customer_id', $user->id);
            } elseif ($role === 'Vendor') {
                // vendor যেসব product এর owner সেসব অর্ডার
                $query->whereHas('items.product', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } else {
                // Admin → সব
            }
        }

        $perPage = (int) $request->query('per_page', 15);

        $orders = $query->paginate($perPage);

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'discount'     => 'nullable|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.product_id'  => 'required|integer|exists:products,id',
            'items.*.variant_id'  => 'nullable|integer|exists:product_variants,id',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        $order = $this->service->create($data, $request->user('api'));

        return response()->json($order, 201);
    }

    public function show(Order $order, Request $request)
    {
        // simple check: শুধুই নিজের order দেখতে পারবে (Admin হলে ছাড় দিতে পারো)
        if ($order->customer_id !== $request->user('api')->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($order->load('items.product', 'items.variant'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        // এখানে role check করতে পারো (Vendor/Admin)
        $order = $this->service->updateStatus($order, $data['status']);

        return response()->json($order);
    }

    public function invoice(Order $order)
    {
        $order->load('customer', 'items.product', 'items.variant');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
        ]);

        return $pdf->download("invoice-{$order->id}.pdf");
    }
}
