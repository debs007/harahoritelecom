<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use Illuminate\Http\Request;

class ExchangeAdminController extends Controller
{
    public function update(Request $request, ExchangeRequest $exchange)
    {
        $request->validate([
            'status'         => 'required|in:pending,verified,approved,rejected',
            'approved_value' => 'nullable|numeric|min:0',
            'admin_notes'    => 'nullable|string|max:500',
        ]);

        $exchange->update([
            'status'         => $request->status,
            'approved_value' => $request->approved_value ?: null,
            'admin_notes'    => $request->admin_notes,
        ]);

        return back()->with('success', 'Exchange request updated.');
    }
}
