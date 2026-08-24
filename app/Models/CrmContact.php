<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmContact extends Model {
    protected $fillable = [
        'user_id','name','email','phone','whatsapp','city','state','pincode',
        'segment','source','contact_type','status','notes','preferences','due_date',
        'last_contacted_at','visit_count','total_spent','total_orders',
    ];
    protected $casts = [
        'preferences'      => 'array',
        'due_date'         => 'date',
        'last_contacted_at'=> 'date',
        'total_spent'      => 'decimal:2',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function interactions() { return $this->hasMany(CrmInteraction::class)->latest('interacted_at'); }
    public function leads()        { return $this->hasMany(CrmLead::class); }
    public function tickets()      { return $this->hasMany(CrmTicket::class); }
    public function campaigns()    { return $this->belongsToMany(CrmCampaign::class,'crm_campaign_contacts')->withPivot('status','sent_at')->withTimestamps(); }

    public function getSegmentLabelAttribute(): string {
        return match($this->segment) {
            'budget'       => 'Budget (₹9K–20K)',
            'mid_range'    => 'Mid-Range (₹20K–40K)',
            'upper_mid'    => 'Upper Mid (₹40K–70K)',
            'premium'      => 'Premium (₹70K–1L)',
            'flagship'     => 'Flagship (₹1L–1.45L)',
            default        => 'Unclassified',
        };
    }

    public function getSegmentColorAttribute(): string {
        return match($this->segment) {
            'budget'    => 'gray',
            'mid_range' => 'blue',
            'upper_mid' => 'indigo',
            'premium'   => 'purple',
            'flagship'  => 'yellow',
            default     => 'gray',
        };
    }

    public static function segmentFromSpend(float $avg): string
    {
        // Read boundaries from CRM settings (DB) so they can be changed via UI
        $get = fn(string $key, float $default) => (float) \App\Models\CrmSetting::get($key, $default);

        return match(true) {
            $avg >= $get('segment_flagship_min', 100001) => 'flagship',
            $avg >= $get('segment_premium_min',   70001) => 'premium',
            $avg >= $get('segment_upper_mid_min', 40001) => 'upper_mid',
            $avg >= $get('segment_mid_min',       20001) => 'mid_range',
            $avg >= $get('segment_budget_min',     9000) => 'budget',
            default                                       => 'unclassified',
        };
    }
}
