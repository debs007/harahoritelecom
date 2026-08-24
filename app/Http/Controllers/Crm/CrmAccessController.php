<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmAccessController extends Controller
{
    public function showForm() {
        if (session('crm_authenticated')) return redirect()->route('crm.dashboard');
        return view('crm.access');
    }

    public function verify(Request $request) {
        $key = config('crm.access_key');
        if (!$key) return back()->withErrors(['code' => 'CRM access key not configured. Set CRM_ACCESS_KEY in your .env file.']);
        if ($request->code !== $key) return back()->withErrors(['code' => 'Invalid access code.'])->withInput();
        session(['crm_authenticated' => true, 'crm_accessed_at' => now()->toDateTimeString()]);
        return redirect()->route('crm.dashboard');
    }

    public function logout() {
        session()->forget(['crm_authenticated','crm_accessed_at']);
        return redirect()->route('crm.access')->with('success', 'CRM session locked.');
    }
}
