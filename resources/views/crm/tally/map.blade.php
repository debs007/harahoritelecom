@extends('crm.layouts.crm')
@section('title','Map Columns')
@section('breadcrumb')<span class="mx-1">/</span><a href="{{ route('crm.tally.index') }}" class="hover:text-slate-700">Tally Import</a><span class="mx-1">/</span><span class="text-slate-700">Map Columns</span>@endsection
@section('content')
<div class="max-w-2xl mx-auto">
  <h1 class="text-2xl font-black text-slate-900 mb-2">Map Tally Columns</h1>
  <p class="text-slate-500 text-sm mb-5">File: <strong>{{ $import->original_name }}</strong> — Match your Tally column names to CRM fields.</p>

  {{-- Preview table --}}
  @if(!empty($preview))
  <div class="card mb-5">
    <h3 class="font-semibold text-slate-800 mb-3 text-sm">📋 Data Preview (first 5 rows)</h3>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead><tr class="bg-slate-50">@foreach($columns as $col)<th class="px-3 py-2 text-left font-bold text-slate-600 whitespace-nowrap">{{ $col }}</th>@endforeach</tr></thead>
        <tbody>@foreach($preview as $row)<tr class="border-t">@foreach($row as $cell)<td class="px-3 py-1.5 text-slate-600 whitespace-nowrap">{{ $cell }}</td>@endforeach</tr>@endforeach</tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- Column mapping form --}}
  <div class="card">
    <form method="POST" action="{{ route('crm.tally.process',$import) }}" class="space-y-4">
      @csrf
      @php $fields = ['map_name'=>'Contact Name *','map_phone'=>'Phone Number','map_email'=>'Email','map_city'=>'City','map_state'=>'State']; @endphp
      @foreach($fields as $fieldName=>$label)
      <div>
        <label class="label">{{ $label }}</label>
        <select name="{{ $fieldName }}" class="input">
          <option value="">— Skip this field —</option>
          @foreach($columns as $col)<option value="{{ $col }}">{{ $col }}</option>@endforeach
        </select>
      </div>
      @endforeach
      <div class="flex gap-3 pt-2">
        <a href="{{ route('crm.tally.index') }}" class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg text-sm text-center">Cancel</a>
        <button type="submit" class="flex-1 crm-btn justify-center">Import Contacts →</button>
      </div>
    </form>
  </div>
</div>
@endsection
