<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedeemGateway;
use App\Models\RedeemMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Models\RedeemRequest;

class RedeemController extends Controller
{
    // Requests Management
    public function requests()
    {
        $stats = [
            'total' => RedeemRequest::count(),
            'pending' => RedeemRequest::where('status', 'pending')->count(),
            'approved' => RedeemRequest::where('status', 'approved')->count(),
            'rejected' => RedeemRequest::where('status', 'rejected')->count(),
        ];

        $requests = RedeemRequest::with(['user', 'method.gateway'])->latest()->paginate(20);
        return view('admin.redeem.requests.index', compact('requests', 'stats'));
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $redeemRequest = RedeemRequest::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);
        
        $redeemRequest->status = $request->status;
        $redeemRequest->save();
        
        return redirect()->back()->with('success', 'Request status updated successfully.');
    }

    // Gateways Management

    public function index()
    {
        $stats = [
            'total' => RedeemGateway::count(),
            'active' => RedeemGateway::where('is_active', true)->count(),
            'inactive' => RedeemGateway::where('is_active', false)->count(),
        ];
        $gateways = RedeemGateway::withCount('methods')->orderBy('priority', 'desc')->get();
        return view('admin.redeem.gateways.index', compact('gateways', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $gateway = new RedeemGateway();
        $gateway->name = $request->name;
        $gateway->priority = $request->priority ?? 0;
        $gateway->is_active = $request->has('is_active');

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/gateways'), $filename);
            $gateway->icon = 'img/gateways/' . $filename;
        }

        $gateway->save();

        return redirect()->back()->with('success', 'Gateway created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $gateway = RedeemGateway::findOrFail($id);
        $gateway->name = $request->name;
        $gateway->priority = $request->priority ?? 0;
        $gateway->is_active = $request->has('is_active');

        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($gateway->icon && File::exists(public_path($gateway->icon))) {
                File::delete(public_path($gateway->icon));
            }
            $file = $request->file('icon');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/gateways'), $filename);
            $gateway->icon = 'img/gateways/' . $filename;
        }

        $gateway->save();

        return redirect()->back()->with('success', 'Gateway updated successfully.');
    }

    public function destroy($id)
    {
        $gateway = RedeemGateway::findOrFail($id);
        
        // Delete icon
        if ($gateway->icon && File::exists(public_path($gateway->icon))) {
            File::delete(public_path($gateway->icon));
        }
        
        $gateway->delete();

        return redirect()->back()->with('success', 'Gateway deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:redeem_gateways,id',
        ]);

        $gateways = RedeemGateway::whereIn('id', $request->ids)->get();

        foreach ($gateways as $gateway) {
            // Delete icon
            if ($gateway->icon && File::exists(public_path($gateway->icon))) {
                File::delete(public_path($gateway->icon));
            }
            $gateway->delete();
        }

        return redirect()->back()->with('success', 'Selected gateways deleted successfully.');
    }

    // Methods Management

    public function methods($gatewayId)
    {
        $gateway = RedeemGateway::findOrFail($gatewayId);
        $methods = $gateway->methods()->orderBy('coin_cost', 'asc')->get();
        
        $stats = [
            'total' => $methods->count(),
            'active' => $methods->where('is_active', true)->count(),
            'inactive' => $methods->where('is_active', false)->count(),
        ];
        
        return view('admin.redeem.methods.index', compact('gateway', 'methods', 'stats'));
    }

    public function storeMethod(Request $request, $gatewayId)
    {
        $gateway = RedeemGateway::findOrFail($gatewayId);

        $request->validate([
            'name' => 'required|string|max:255',
            'input_hint' => 'required|string|max:255',
            'coin_cost' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $method = new RedeemMethod();
        $method->redeem_gateway_id = $gateway->id;
        $method->name = $request->name;
        $method->input_hint = $request->input_hint;
        $method->coin_cost = $request->coin_cost;
        $method->amount = $request->amount;
        $method->currency = $request->currency;
        $method->is_active = $request->has('is_active');
        $method->save();

        return redirect()->back()->with('success', 'Redeem method added successfully.');
    }

    public function updateMethod(Request $request, $id)
    {
        $method = RedeemMethod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'input_hint' => 'required|string|max:255',
            'coin_cost' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $method->name = $request->name;
        $method->input_hint = $request->input_hint;
        $method->coin_cost = $request->coin_cost;
        $method->amount = $request->amount;
        $method->currency = $request->currency;
        $method->is_active = $request->has('is_active');
        $method->save();

        return redirect()->back()->with('success', 'Redeem method updated successfully.');
    }

    public function destroyMethod($id)
    {
        $method = RedeemMethod::findOrFail($id);
        $method->delete();

        return redirect()->back()->with('success', 'Redeem method deleted successfully.');
    }
}
