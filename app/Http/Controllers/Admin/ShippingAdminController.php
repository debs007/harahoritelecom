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
        $request->validate([
            'name'           => 'required|string|max:100',
            'states_raw'     => 'required|string',
            'rate'           => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'is_active'      => 'nullable',
        ]);

        $states = $this->parseStates($request->states_raw);

        ShippingZone::create([
            'name'           => $request->name,
            'states'         => $states,
            'rate'           => $request->rate,
            'free_above'     => $request->free_above ?: null,
            'estimated_days' => $request->estimated_days,
            'is_active'      => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping zone created successfully.');
    }

    public function show($id)
    {
        $shipping = ShippingZone::findOrFail($id);
        return view('admin.shipping.edit', compact('shipping'));
    }

    public function edit($id)
    {
        $shipping = ShippingZone::findOrFail($id);
        return view('admin.shipping.edit', compact('shipping'));
    }

    public function update(Request $request, $id)
    {
        $shipping = ShippingZone::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:100',
            'states_raw'     => 'required|string',
            'rate'           => 'required|numeric|min:0',
            'free_above'     => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
            'is_active'      => 'nullable',
        ]);

        $states = $this->parseStates($request->states_raw);

        $shipping->update([
            'name'           => $request->name,
            'states'         => $states,
            'rate'           => $request->rate,
            'free_above'     => $request->free_above ?: null,
            'estimated_days' => $request->estimated_days,
            'is_active'      => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.shipping.index')
            ->with('success', 'Shipping zone updated successfully.');
    }

    public function destroy($id)
    {
        $shipping = ShippingZone::findOrFail($id);
        $shipping->delete();
        return back()->with('success', 'Shipping zone deleted.');
    }

    /**
     * Parse a textarea of states (newline or comma separated) into an array.
     */
    private function parseStates(string $raw): array
    {
        $states = preg_split('/[\n\r,]+/', $raw);
        $states = array_map('trim', $states);
        $states = array_filter($states, fn($s) => $s !== '');
        return array_values($states);
    }
}
