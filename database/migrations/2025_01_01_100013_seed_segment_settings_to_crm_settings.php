<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        $segments = [
            ['key'=>'segment_budget_min',    'value'=>'9000',   'label'=>'Budget — Min (₹)',      'type'=>'number'],
            ['key'=>'segment_budget_max',    'value'=>'20000',  'label'=>'Budget — Max (₹)',      'type'=>'number'],
            ['key'=>'segment_mid_min',       'value'=>'20001',  'label'=>'Mid-Range — Min (₹)',   'type'=>'number'],
            ['key'=>'segment_mid_max',       'value'=>'40000',  'label'=>'Mid-Range — Max (₹)',   'type'=>'number'],
            ['key'=>'segment_upper_mid_min', 'value'=>'40001',  'label'=>'Upper Mid — Min (₹)',   'type'=>'number'],
            ['key'=>'segment_upper_mid_max', 'value'=>'70000',  'label'=>'Upper Mid — Max (₹)',   'type'=>'number'],
            ['key'=>'segment_premium_min',   'value'=>'70001',  'label'=>'Premium — Min (₹)',     'type'=>'number'],
            ['key'=>'segment_premium_max',   'value'=>'100000', 'label'=>'Premium — Max (₹)',     'type'=>'number'],
            ['key'=>'segment_flagship_min',  'value'=>'100001', 'label'=>'Flagship — Min (₹)',    'type'=>'number'],
            ['key'=>'segment_flagship_max',  'value'=>'145000', 'label'=>'Flagship — Max (₹)',    'type'=>'number'],
        ];
        foreach ($segments as $s) {
            DB::table('crm_settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, ['created_at'=>now(),'updated_at'=>now()])
            );
        }
    }
    public function down(): void {
        DB::table('crm_settings')->whereIn('key', [
            'segment_budget_min','segment_budget_max','segment_mid_min','segment_mid_max',
            'segment_upper_mid_min','segment_upper_mid_max','segment_premium_min','segment_premium_max',
            'segment_flagship_min','segment_flagship_max',
        ])->delete();
    }
};
