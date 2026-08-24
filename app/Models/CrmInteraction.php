<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmInteraction extends Model {
    protected $fillable = ['crm_contact_id','type','description','outcome','interacted_at'];
    protected $casts    = ['interacted_at' => 'datetime'];
    public function contact() { return $this->belongsTo(CrmContact::class,'crm_contact_id'); }
}
