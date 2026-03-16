<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingAdminController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::latest()->get();
        return view('admin.shipping.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.shipping.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'states'         => 'required|array|min:1',
            'states.*'       => 'string',
            'rate'           => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'is_active'      => 'nullable|boolean',
        ]);

        ShippingZone::create($data);
        return redirect()->route('admin.shipping.index')->with('success', 'Shipping zone created.');
    }

    public function edit(ShippingZone $shipping)
    {
        return view('admin.shipping.edit', compact('shipping'));
    }

    public function update(Request $request, ShippingZone $shipping)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'states'         => 'required|array|min:1',
            'rate'           => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'is_active'      => 'nullable|boolean',
        ]);

        $shipping->update($data);
        return redirect()->route('admin.shipping.index')->with('success', 'Shipping zone updated.');
    }

    public function destroy(ShippingZone $shipping)
    {
        $shipping->delete();
        return back()->with('success', 'Shipping zone deleted.');
    }

    public function show(ShippingZone $shipping) { return $this->edit($shipping); }
}
