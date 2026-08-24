<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model {
    protected $fillable = ['crm_contact_id','title','value','stage','score','source','expected_close_date','notes','product_interest'];
    protected $casts    = ['value'=>'decimal:2','expected_close_date'=>'date'];
    public function contact() { return $this->belongsTo(CrmContact::class,'crm_contact_id'); }

    public function getStageLabelAttribute(): string {
        return match($this->stage) {
            'new'         => 'New',
            'contacted'   => 'Contacted',
            'qualified'   => 'Qualified',
            'proposal'    => 'Proposal Sent',
            'negotiation' => 'Negotiation',
            'won'         => 'Won',
            'lost'        => 'Lost',
            default       => $this->stage,
        };
    }
    public function getStageColorAttribute(): string {
        return match($this->stage) {
            'new'         => 'gray',
            'contacted'   => 'blue',
            'qualified'   => 'indigo',
            'proposal'    => 'purple',
            'negotiation' => 'orange',
            'won'         => 'green',
            'lost'        => 'red',
            default       => 'gray',
        };
    }
}
