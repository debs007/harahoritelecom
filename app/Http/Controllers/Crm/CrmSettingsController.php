<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmSetting, CrmContact, User};
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class CrmSettingsController extends Controller
{
    public function index()
    {
        $settings = CrmSetting::all()->keyBy('key');
        return view('crm.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Segment boundaries
            'segment_budget_min'    => 'required|numeric|min:0',
            'segment_budget_max'    => 'required|numeric|min:1',
            'segment_mid_min'       => 'required|numeric|min:0',
            'segment_mid_max'       => 'required|numeric|min:1',
            'segment_upper_mid_min' => 'required|numeric|min:0',
            'segment_upper_mid_max' => 'required|numeric|min:1',
            'segment_premium_min'   => 'required|numeric|min:0',
            'segment_premium_max'   => 'required|numeric|min:1',
            'segment_flagship_min'  => 'required|numeric|min:0',
            'segment_flagship_max'  => 'required|numeric|min:1',
            // Loyalty
            'loyalty_point_value'    => 'required|numeric|min:0.01',
            'loyalty_points_per_100' => 'required|numeric|min:0.1',
            'loyalty_max_redemption' => 'required|numeric|min:1|max:100',
        ]);

        foreach ($data as $key => $value) {
            CrmSetting::set($key, $value);
        }

        return back()->with('success', 'Settings saved. Run "Re-classify All Contacts" to apply new segment ranges.');
    }

    /**
     * Re-classify all contacts based on updated segment boundaries.
     * Fixes feature #6 — reclassify even manually created contacts.
     */
    public function reclassify()
    {
        $count = 0;

        // Reclassify users who have a CRM contact
        $users = User::where('role','customer')
            ->whereHas('crmContact')
            ->with(['orders','addresses','crmContact'])
            ->get();

        foreach ($users as $user) {
            $totalSpent  = $user->orders()->where('status','delivered')->sum('total');
            $totalOrders = $user->orders()->where('status','delivered')->count();
            $avgSpend    = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
            $segment     = CrmContact::segmentFromSpend($avgSpend);

            // Update contact (even manually created ones)
            $contact = $user->crmContact;
            $updateData = [
                'segment'      => $segment,
                'total_spent'  => $totalSpent,
                'total_orders' => $totalOrders,
            ];

            // Fix #7 — if user has orders, ensure contact_type = buyer
            if ($totalOrders > 0) {
                $updateData['contact_type'] = 'buyer';
                $updateData['status']       = 'active';
            }

            $contact->update($updateData);
            $user->update(['crm_segment' => $segment]);
            $count++;
        }

        // Also reclassify tally/manual contacts linked to a user
        CrmContact::whereIn('contact_type', ['tally_import','manual','prospect'])
            ->whereNotNull('user_id')
            ->with('user.orders')
            ->get()
            ->each(function ($contact) {
                if (!$contact->user) return;
                $totalSpent  = $contact->user->orders()->where('status','delivered')->sum('total');
                $totalOrders = $contact->user->orders()->where('status','delivered')->count();
                $avgSpend    = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
                $contact->update([
                    'segment'      => CrmContact::segmentFromSpend($avgSpend),
                    'total_spent'  => $totalSpent,
                    'total_orders' => $totalOrders,
                    'contact_type' => $totalOrders > 0 ? 'buyer' : $contact->contact_type,
                    'status'       => $totalOrders > 0 ? 'active' : $contact->status,
                ]);
            });

        return back()->with('success', "Re-classified {$count} contacts based on current segment settings.");
    }
}
