@extends('crm.layouts.crm')
@section('title','Tally Import')
@section('breadcrumb')<span class="mx-1">/</span><span class="text-slate-700">Tally Import</span>@endsection
@section('content')
<h1 class="text-2xl font-black text-slate-900 mb-5">📁 Tally Data Import</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Upload Form --}}
  <div class="card">
    <h3 class="font-bold text-slate-800 mb-1">Upload Tally Export</h3>
    <p class="text-xs text-slate-500 mb-4">Supports CSV, Excel (.xlsx/.xls) files exported from Tally ERP. You'll map columns in the next step.</p>
    <form method="POST" action="{{ route('crm.tally.upload') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div>
        <div onclick="document.getElementById('tally-file').click()"
             class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-teal-400 hover:bg-teal-50/30 transition group">
          <div class="text-4xl mb-2 group-hover:scale-110 transition">📊</div>
          <p class="text-sm text-slate-600 font-medium">Drop Tally file here or click to browse</p>
          <p class="text-xs text-slate-400 mt-1">CSV, XLSX, XLS — max 10MB</p>
        </div>
        <input type="file" id="tally-file" name="tally_file" accept=".csv,.xlsx,.xls,.txt" class="hidden"
               onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? 'No file chosen'">
        <p id="file-name" class="text-xs text-center text-slate-400 mt-2">No file chosen</p>
        @error('tally_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <button type="submit" class="crm-btn w-full justify-center">Upload & Preview →</button>
    </form>

    <div class="mt-5 pt-5 border-t border-slate-100">
      <h4 class="font-semibold text-slate-700 text-sm mb-2">Expected CSV format:</h4>
      <div class="bg-slate-900 text-teal-300 rounded-lg p-3 text-xs font-mono overflow-x-auto">
        Name,Phone,Email,City,State<br>
        Rahul Sharma,9876543210,r@mail.com,Mumbai,MH<br>
        Priya Devi,9123456789,,Delhi,DL
      </div>
      <p class="text-xs text-slate-400 mt-2">Column names can be anything — you'll map them after upload.</p>
    </div>
  </div>

  {{-- Import History --}}
  <div class="lg:col-span-2">
    <h3 class="font-bold text-slate-800 mb-3">Import History</h3>
    <div class="space-y-3">
      @forelse($imports as $import)
      <div class="card p-4">
        <div class="flex items-start justify-between gap-4 flex-wrap">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="badge {{ match($import->status){'completed'=>'badge-green','failed'=>'badge-red','processing'=>'badge-blue',default=>'badge-gray'} }}">{{ ucfirst($import->status) }}</span>
              <p class="font-semibold text-slate-800 text-sm">{{ $import->original_name }}</p>
            </div>
            <div class="flex flex-wrap gap-4 text-xs text-slate-500">
              <span>📥 {{ number_format($import->total_rows) }} total rows</span>
              <span class="text-teal-600 font-bold">✅ {{ number_format($import->imported_rows) }} imported</span>
              @if($import->skipped_rows > 0)<span class="text-orange-500">⏭ {{ number_format($import->skipped_rows) }} skipped</span>@endif
              @if($import->processed_at)<span>🕐 {{ $import->processed_at->diffForHumans() }}</span>@endif
            </div>
            @if($import->error_log)
            <details class="mt-2">
              <summary class="text-xs text-red-500 cursor-pointer font-semibold">⚠ View errors</summary>
              <pre class="text-xs text-red-400 mt-1 bg-red-50 rounded p-2 max-h-24 overflow-auto">{{ $import->error_log }}</pre>
            </details>
            @endif
          </div>
          <form method="POST" action="{{ route('crm.tally.destroy',$import) }}" onsubmit="return confirm('Delete this import record?')">@csrf @method('DELETE')
            <button class="text-xs text-red-400 hover:text-red-600 font-semibold">Delete</button>
          </form>
        </div>
      </div>
      @empty
      <div class="card text-center py-10 text-slate-400">No imports yet. Upload your first Tally file above.</div>
      @endforelse
      @if($imports->hasPages())<div class="mt-4">{{ $imports->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
