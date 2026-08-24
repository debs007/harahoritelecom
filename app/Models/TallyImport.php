<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TallyImport extends Model {
    protected $fillable = ['filename','original_name','status','total_rows','imported_rows','skipped_rows','error_log','column_map','processed_at'];
    protected $casts    = ['column_map'=>'array','processed_at'=>'datetime'];
}
