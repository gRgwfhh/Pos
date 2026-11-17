@extends('layouts.app')

@section('title', 'Audit log')

@section('content')
    <div class="page-head">
        <div>
            <h1>Audit log</h1>
            <div class="muted">Recent actions performed by users. Password values are never recorded.</div>
        </div>
    </div>

    <section class="panel table-wrap">
        <table>
            <thead><tr><th>Date and time</th><th>User</th><th>Action</th><th>Details</th></tr></thead>
            <tbody>
            @forelse($auditLogs as $auditLog)
                <tr>
                    <td>{{ $auditLog->occurred_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $auditLog->actor_username ?? 'Unknown user' }}</td>
                    <td>{{ $auditLog->event }}</td>
                    <td>
                        {{ $auditLog->description }}
                        @if($auditLog->metadata)
                            <details>
                                <summary>View recorded details</summary>
                                <pre style="white-space:pre-wrap">{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No activity recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $auditLogs->links() }}</div>
    </section>
@endsection
