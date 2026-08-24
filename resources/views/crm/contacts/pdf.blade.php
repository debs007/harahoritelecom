<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 0; }
    h1   { font-size: 18px; font-weight: 900; color: #0f172a; margin-bottom: 4px; }
    p.sub { font-size: 11px; color: #64748b; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #0d9488; color: white; }
    th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
    td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 99px; font-size: 9px; font-weight: 700; }
    .seg-budget    { background: #f1f5f9; color: #475569; }
    .seg-mid_range { background: #dbeafe; color: #1e40af; }
    .seg-upper_mid { background: #e0e7ff; color: #3730a3; }
    .seg-premium   { background: #ede9fe; color: #5b21b6; }
    .seg-flagship  { background: #fef3c7; color: #92400e; }
    .seg-unclassified { background: #f1f5f9; color: #94a3b8; }
    .footer { margin-top: 14px; font-size: 9px; color: #94a3b8; }
</style>
</head>
<body>
    <h1>Harahori CRM — Contact Export</h1>
    <p class="sub">
        Generated: {{ now()->format('d M Y, h:i A') }}
        &nbsp;|&nbsp; Tab: {{ ucfirst($tab) }}
        &nbsp;|&nbsp; Total: {{ $contacts->count() }} contacts
        &nbsp;|&nbsp; Sorted by highest spend
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>City / State</th>
                <th>Segment</th>
                <th>Type</th>
                <th>Total Spent</th>
                <th>Orders</th>
                <th>Status</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $c->name }}</strong></td>
                <td>{{ $c->phone ?? '—' }}</td>
                <td>{{ $c->email ?? '—' }}</td>
                <td>{{ collect([$c->city, $c->state])->filter()->implode(', ') ?: '—' }}</td>
                <td>
                    <span class="badge seg-{{ $c->segment }}">
                        {{ $segDefs[$c->segment] ?? $c->segment }}
                    </span>
                </td>
                <td>{{ ucfirst(str_replace('_',' ',$c->contact_type ?? 'prospect')) }}</td>
                <td>₹{{ number_format($c->total_spent) }}</td>
                <td>{{ $c->total_orders }}</td>
                <td>{{ ucfirst($c->status) }}</td>
                <td>{{ $c->due_date?->format('d M Y') ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Harahori Telecom CRM &nbsp;|&nbsp; Confidential &nbsp;|&nbsp; {{ now()->format('Y') }}</p>
</body>
</html>
