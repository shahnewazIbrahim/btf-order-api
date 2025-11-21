<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Orders\CreateOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected CreateOrder $createOrder,
        protected UpdateOrderStatus $updateOrderStatus
    ) {}

    public function index(Request $request)
    {
        $user = auth('api')->user();

        $query = Order::with('customer', 'items.product', 'items.variant')
            ->orderByDesc('id');

        if ($user) {
            $role = $user->role->name ?? null;

            if ($role === 'Customer') {
                $query->where('customer_id', $user->id);
            } elseif ($role === 'Vendor') {
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
        $this->authorizeOrderPlacement($request->user('api'));

        $data = $request->validate([
            'discount'     => 'nullable|numeric|min:0',
            'items'        => 'required|array|min:1',
            'items.*.product_id'  => 'required|integer|exists:products,id',
            'items.*.variant_id'  => 'nullable|integer|exists:product_variants,id',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        $order = ($this->createOrder)($data, $request->user('api'));

        return response()->json($order, 201);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorizeOrderView($request->user('api'), $order);

        return response()->json($order->load('items.product', 'items.variant'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        $this->authorizeOrderStatusUpdate($request->user('api'), $order);
        $order = ($this->updateOrderStatus)($order, $data['status']);

        return response()->json($order);
    }

    public function invoice(Order $order)
    {
        $this->authorizeOrderView(auth('api')->user(), $order);
        $order->load('customer', 'items.product', 'items.variant');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
        ]);

        return $pdf->download("invoice-{$order->id}.pdf");
    }

    protected function authorizeOrderPlacement($user): void
    {
        if (! $user || ! $user->role) {
            abort(403, 'Unauthorized');
        }

        $role = $user->role->name;

        if (! in_array($role, ['Customer', 'Admin'], true)) {
            abort(403, 'Only customers can place orders');
        }
    }

    protected function authorizeOrderView($user, Order $order): void
    {
        if (! $user || ! $user->role) {
            abort(403);
        }

        $role = $user->role->name;

        if ($role === 'Admin') {
            return;
        }

        if ($role === 'Customer' && $order->customer_id === $user->id) {
            return;
        }

        if ($role === 'Vendor') {
            $hasVendorItem = $order->items()
                ->whereHas('product', fn($q) => $q->where('user_id', $user->id))
                ->exists();

            if ($hasVendorItem) {
                return;
            }
        }

        abort(403);
    }

    protected function authorizeOrderStatusUpdate($user, Order $order): void
    {
        if (! $user || ! $user->role) {
            abort(403);
        }

        $role = $user->role->name;

        if ($role === 'Admin') {
            return;
        }

        if ($role === 'Vendor') {
            $hasVendorItem = $order->items()
                ->whereHas('product', fn($q) => $q->where('user_id', $user->id))
                ->exists();

            if ($hasVendorItem) {
                return;
            }
        }

        abort(403);
    }
}
