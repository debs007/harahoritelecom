<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmCampaign extends Model {
    protected $fillable = ['name','type','aisensy_campaign','status','message_template','target_segments','scheduled_at','sent_at','total_recipients','sent_count','delivered_count','conversion_count'];
    protected $casts    = ['target_segments'=>'array','scheduled_at'=>'datetime','sent_at'=>'datetime'];
    public function contacts() { return $this->belongsToMany(CrmContact::class,'crm_campaign_contacts')->withPivot('status','sent_at')->withTimestamps(); }
    public function getDeliveryRateAttribute(): float {
        return $this->sent_count > 0 ? round($this->delivered_count / $this->sent_count * 100, 1) : 0;
    }
    public function getConversionRateAttribute(): float {
        return $this->delivered_count > 0 ? round($this->conversion_count / $this->delivered_count * 100, 1) : 0;
    }
}
