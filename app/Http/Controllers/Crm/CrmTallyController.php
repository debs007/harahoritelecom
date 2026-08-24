<?php
namespace App\Http\Controllers\Crm;
use App\Http\Controllers\Controller;
use App\Models\TallyImport;
use Illuminate\Http\Request;

class CrmTallyController extends Controller
{
    public function index()
    {
        $imports = TallyImport::latest()->paginate(20);
        return view('crm.tally.index', compact('imports'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'tally_file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240',
        ]);

        $file = $request->file('tally_file');
        $stored = $file->store('tally_imports','local');

        // Peek at first few rows to detect columns
        $preview = [];
        $columns = [];
        try {
            $content = file_get_contents(storage_path('app/'.$stored));
            $lines   = array_filter(array_map('trim', explode("\n", $content)));
            if (!empty($lines)) {
                $columns = str_getcsv(array_shift($lines));
                foreach (array_slice($lines, 0, 5) as $line) {
                    $preview[] = str_getcsv($line);
                }
            }
        } catch (\Throwable) {}

        $import = TallyImport::create([
            'filename'      => $stored,
            'original_name' => $file->getClientOriginalName(),
            'status'        => 'pending',
            'total_rows'    => max(0, count($preview) - 1),
            'column_map'    => $columns,
        ]);

        return view('crm.tally.map', compact('import','columns','preview'));
    }

    public function process(Request $request, TallyImport $import)
    {
        $request->validate([
            'map_name'  => 'nullable|string',
            'map_phone' => 'nullable|string',
            'map_email' => 'nullable|string',
            'map_city'  => 'nullable|string',
            'map_state' => 'nullable|string',
        ]);

        $import->update([
            'status'     => 'processing',
            'column_map' => array_merge($import->column_map ?? [], [
                'name'  => $request->map_name,
                'phone' => $request->map_phone,
                'email' => $request->map_email,
                'city'  => $request->map_city,
                'state' => $request->map_state,
            ]),
        ]);

        try {
            $content  = file_get_contents(storage_path('app/'.$import->filename));
            $lines    = array_filter(array_map('trim', explode("\n", $content)));
            $headers  = str_getcsv(array_shift($lines));
            $map      = $import->column_map;

            $idx = [];
            foreach (['name','phone','email','city','state'] as $field) {
                $col = $map[$field] ?? null;
                $idx[$field] = $col ? array_search($col, $headers) : false;
            }

            $imported = 0; $skipped = 0; $errors = [];
            foreach ($lines as $line) {
                $row = str_getcsv($line);
                if (empty($row)) { $skipped++; continue; }

                $name  = $idx['name']  !== false ? ($row[$idx['name']]  ?? null) : null;
                $phone = $idx['phone'] !== false ? ($row[$idx['phone']] ?? null) : null;
                $email = $idx['email'] !== false ? ($row[$idx['email']] ?? null) : null;
                $city  = $idx['city']  !== false ? ($row[$idx['city']]  ?? null) : null;
                $state = $idx['state'] !== false ? ($row[$idx['state']] ?? null) : null;

                if (empty($name) && empty($phone)) { $skipped++; continue; }

                try {
                    \App\Models\CrmContact::firstOrCreate(
                        ['phone' => $phone],
                        [
                            'name'    => $name  ?? 'Unknown',
                            'email'   => $email,
                            'phone'   => $phone,
                            'city'    => $city,
                            'state'   => $state,
                            'source'       => 'tally_import',
                            'contact_type' => 'tally_import',
                            'segment' => 'unclassified',
                            'status'  => 'prospect',
                        ]
                    );
                    $imported++;
                } catch (\Throwable $e) {
                    $skipped++;
                    $errors[] = "Row error: ".$e->getMessage();
                }
            }

            $import->update([
                'status'        => 'completed',
                'imported_rows' => $imported,
                'skipped_rows'  => $skipped,
                'total_rows'    => $imported + $skipped,
                'error_log'     => !empty($errors) ? implode("\n", array_slice($errors,0,20)) : null,
                'processed_at'  => now(),
            ]);

            return redirect()->route('crm.tally.index')
                ->with('success',"Import complete: {$imported} contacts imported, {$skipped} skipped.");

        } catch (\Throwable $e) {
            $import->update(['status'=>'failed','error_log'=>$e->getMessage()]);
            return back()->withErrors(['msg'=>'Processing failed: '.$e->getMessage()]);
        }
    }

    public function destroy(TallyImport $import)
    {
        \Illuminate\Support\Facades\Storage::disk('local')->delete($import->filename);
        $import->delete();
        return back()->with('success','Import record deleted.');
    }
}
