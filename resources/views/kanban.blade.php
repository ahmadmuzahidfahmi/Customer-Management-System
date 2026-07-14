@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Leads Pipeline</h1>
    <a href="{{ route('leads') }}" class="text-cyan-600 hover:text-cyan-700 text-sm">
        View as Table
    </a>
</div>

<div class="flex gap-4 overflow-x-auto pb-4">

    @foreach($statuses as $status)
    <div class="bg-gray-50 rounded-lg w-72 flex-shrink-0">

        <div class="p-3 border-b bg-white rounded-t-lg flex justify-between items-center">
            <h2 class="font-semibold text-gray-700 text-sm">{{ $status }}</h2>
            <span class="text-xs text-gray-400">
                {{ $leadsByStatus->get($status, collect())->count() }}
            </span>
        </div>

        <div
            class="kanban-column p-2 space-y-2 min-h-[200px]"
            data-status="{{ $status }}">

            @foreach($leadsByStatus->get($status, collect()) as $lead)
            <div
                class="kanban-card select-none bg-white rounded-lg shadow-sm border p-3 cursor-move"
                data-id="{{ $lead->Lead_ID }}">

                <p class="font-medium text-gray-800 text-sm">{{ $lead->Lead_Name }}</p>

                <p class="text-xs text-gray-500 mt-1">
                    {{ $lead->company->Company_Name ?? 'No Company' }}
                </p>

                @if($lead->Estimated_Value)
                <p class="text-xs text-cyan-600 font-semibold mt-1">
                    ${{ number_format($lead->Estimated_Value) }}
                </p>
                @endif

                <p class="text-xs text-gray-400 mt-2">
                    {{ $lead->Status_Changed_At?->diffForHumans() ?? '—' }}
                </p>

                <a href="{{ route('leads.show', $lead->Lead_ID) }}"
                   class="text-xs text-cyan-600 hover:text-cyan-800 mt-2 inline-block">
                    View
                </a>
            </div>
            @endforeach

        </div>
    </div>
    @endforeach

</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.kanban-column').forEach(column => {
        console.log('Sortable initialized on:', column.dataset.status);

        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            onEnd: (evt) => {
                const leadId = evt.item.dataset.id;
                const newStatus = evt.to.dataset.status;
                const newPosition = evt.newIndex + 1;

                fetch("{{ route('leads.kanban.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        Lead_ID: leadId,
                        Status: newStatus,
                        Position: newPosition,
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => console.log('Saved:', data))
                .catch(error => {
                    console.error('Failed to save card position:', error);
                    alert('Could not save the change — reloading.');
                    location.reload();
                });
            }
        });
    });
});
</script>
@endpush