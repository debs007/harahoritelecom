<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmTicket extends Model {
    protected $fillable = ['ticket_number','crm_contact_id','order_id','subject','description','status','priority','category','sla_due_at','resolved_at','resolution'];
    protected $casts    = ['sla_due_at'=>'datetime','resolved_at'=>'datetime'];
    public function contact() { return $this->belongsTo(CrmContact::class,'crm_contact_id'); }
    public function order()   { return $this->belongsTo(Order::class); }

    public static function generateNumber(): string {
        return 'TKT-'.strtoupper(substr(uniqid(),-6)).'-'.now()->format('Ymd');
    }
    public function isOverdue(): bool {
        return $this->sla_due_at && $this->sla_due_at->isPast() && !in_array($this->status,['resolved','closed']);
    }
}
