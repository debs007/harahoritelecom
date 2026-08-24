<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\{CrmCampaign, CrmContact};
use App\Services\{LoyaltyService, AiSensyService};
use Illuminate\Http\Request;

class CrmCampaignController extends Controller
{
    public function index()
    {
        $campaigns    = CrmCampaign::latest()->paginate(15);
        $contacts     = CrmContact::orderBy('name')->get(['id','name','phone','whatsapp','segment']);
        $aiSensyReady = !empty(config('services.aisensy.campaign_key'));
        return view('crm.campaigns.index', compact('campaigns','contacts','aiSensyReady'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:200',
            'type'             => 'required|in:sms,whatsapp,email',
            'aisensy_campaign' => 'nullable|string|max:200',
            'message_template' => 'required|string',
            'target_segments'  => 'nullable|array',
            'scheduled_at'     => 'nullable|date',
        ]);

        $q = CrmContact::where('status','active');
        if (!empty($data['target_segments'])) $q->whereIn('segment', $data['target_segments']);
        $data['total_recipients'] = $q->count();

        CrmCampaign::create($data);
        return back()->with('success', 'Campaign created with ' . $data['total_recipients'] . ' recipients.');
    }

    /**
     * Launch campaign — auto-send via AiSensy if campaign name is set,
     * otherwise fall back to manual WhatsApp link page.
     */
    public function launch(Request $request, CrmCampaign $campaign)
    {
        if ($campaign->status === 'completed') {
            return back()->withErrors(['msg' => 'Campaign already completed.']);
        }

        $q = CrmContact::with('user')->where('status', 'active');
        if (!empty($campaign->target_segments)) {
            $q->whereIn('segment', $campaign->target_segments);
        }
        $contacts = $q->get();

        // ── AUTO SEND via AiSensy ─────────────────────────────────────────
        $aiSensyCampaign = $campaign->aisensy_campaign ?? null;

        if ($campaign->type === 'whatsapp' && $aiSensyCampaign && config('services.aisensy.campaign_key')) {
            return $this->autoSend($campaign, $contacts, $aiSensyCampaign);
        }

        // ── MANUAL fallback (link page) ───────────────────────────────────
        $links = [];
        foreach ($contacts as $c) {
            $phone = $c->whatsapp ?: $c->phone;
            if (!$phone) continue;
            $msg   = $this->parseTemplate($campaign->message_template, $c);
            $links[] = [
                'contact' => $c->name,
                'phone'   => $phone,
                'link'    => LoyaltyService::buildWhatsappLink($phone, $msg),
                'msg'     => $msg,
            ];
        }

        $campaign->update([
            'status'           => 'running',
            'sent_at'          => now(),
            'sent_count'       => count($links),
            'total_recipients' => count($links),
        ]);

        return view('crm.campaigns.launch', compact('campaign', 'links'));
    }

    /**
     * Auto-send all messages via AiSensy and redirect with results.
     */
    private function autoSend(CrmCampaign $campaign, $contacts, string $aiSensyCampaignName)
    {
        $campaign->update(['status' => 'running', 'sent_at' => now(), 'total_recipients' => $contacts->count()]);

        $bulk = [];
        foreach ($contacts as $c) {
            $phone = $c->whatsapp ?: $c->phone;
            if (!$phone) continue;

            $points   = $c->user?->loyalty_points ?? 0;
            $inrValue = number_format($points * LoyaltyService::pointValue(), 2);

            $bulk[] = [
                'name'   => $c->name,
                'phone'  => $phone,
                // Template params — order must match your AiSensy template variables
                // {{1}} = name, {{2}} = points, {{3}} = INR value
                'params' => [
                    $c->name,
                    (string) $points,
                    '₹' . $inrValue,
                ],
            ];
        }

        $result = AiSensyService::sendBulk($bulk, $aiSensyCampaignName);

        $campaign->update([
            'sent_count'      => $result['sent'],
            'delivered_count' => $result['sent'],
            'status'          => 'completed',
        ]);

        $msg = "✅ Campaign sent! {$result['sent']} delivered, {$result['failed']} failed.";
        if (!empty($result['errors'])) {
            $msg .= ' Errors: ' . implode(' | ', array_slice($result['errors'], 0, 3));
        }

        return redirect()->route('crm.campaigns.index')->with('success', $msg);
    }

    public function markComplete(CrmCampaign $campaign)
    {
        $campaign->update(['status' => 'completed']);
        return redirect()->route('crm.campaigns.index')->with('success', 'Campaign marked complete.');
    }

    /**
     * Test AiSensy API connection.
     */
    public function testConnection()
    {
        $result = AiSensyService::testConnection();
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    private function parseTemplate(string $template, CrmContact $c): string
    {
        $points   = $c->user?->loyalty_points ?? 0;
        $inrValue = number_format($points * LoyaltyService::pointValue(), 2);
        return str_replace(
            ['{{name}}', '{{points}}', '{{inr}}', '{{segment}}', '{{phone}}'],
            [$c->name, $points, '₹'.$inrValue, $c->segment_label, $c->phone ?? ''],
            $template
        );
    }
}
