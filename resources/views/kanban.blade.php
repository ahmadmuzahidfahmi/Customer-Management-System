@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Leads Pipeline</h1>
    <a href="{{ route('leads') }}" class="text-cyan-600 hover:text-cyan-700 text-sm">
        View as Table
    </a>
</div>

<div class="mb-4">
    <input
        type="text"
        id="kanban-search"
        placeholder="Search leads by name or company..."
        class="w-full sm:w-80 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
    >
</div>

<div class="flex gap-4 overflow-x-auto pb-4">

    @php
    $statusColors = [
        'New' => ['header' => 'bg-gray-100', 'border' => 'border-gray-300', 'badge' => 'bg-gray-100 text-gray-700'],
        'Contacted' => ['header' => 'bg-amber-100', 'border' => 'border-amber-300', 'badge' => 'bg-amber-100 text-amber-700'],
        'Qualified' => ['header' => 'bg-blue-100', 'border' => 'border-blue-300', 'badge' => 'bg-blue-100 text-blue-700'],
        'Won' => ['header' => 'bg-green-100', 'border' => 'border-green-300', 'badge' => 'bg-green-100 text-green-700'],
        'Lost' => ['header' => 'bg-red-100', 'border' => 'border-red-300', 'badge' => 'bg-red-100 text-red-700'],
    ];
    // Won/Lost leads are already "done" - collapse them by default so the
    // board isn't dominated by leads nobody needs to act on anymore.
    $collapsedByDefault = ['Won', 'Lost'];
    @endphp

    @foreach($statuses as $status)
    <div class="bg-gray-50 rounded-lg w-72 flex-shrink-0 kanban-column-wrapper" data-status-wrapper="{{ $status }}">

        <div class="p-3 border-b bg-white rounded-t-lg flex justify-between items-center">
            <button
                type="button"
                class="kanban-toggle flex items-center gap-1 font-semibold text-gray-700 text-sm"
                data-status="{{ $status }}"
            >
                <svg class="kanban-toggle-icon w-3 h-3 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                {{ $status }}
            </button>
            <span class="text-xs text-gray-400 kanban-count" data-status-count="{{ $status }}">
                {{ $leadsByStatus->get($status, collect())->count() }}
            </span>
        </div>

        <div
            class="kanban-column p-2 space-y-2 min-h-[80px] max-h-[65vh] overflow-y-auto"
            data-status="{{ $status }}"
            data-default-collapsed="{{ in_array($status, $collapsedByDefault) ? '1' : '0' }}"
        >

            @foreach($leadsByStatus->get($status, collect()) as $lead)
            <div
                class="kanban-card select-none bg-white rounded-lg shadow-sm border-l-4 border p-3 cursor-move {{ $statusColors[$status]['border'] }}"
                data-id="{{ $lead->Lead_ID }}"
                data-name="{{ strtolower($lead->Lead_Name) }}"
                data-company="{{ strtolower($lead->company->Company_Name ?? '') }}"
            >

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
const statusBorderColors = @json(collect($statusColors)->map(fn($c) => $c['border']));
const COLLAPSE_STORAGE_KEY = 'kanban-collapsed-columns';

function loadCollapsedState() {
    try {
        const stored = localStorage.getItem(COLLAPSE_STORAGE_KEY);
        if (stored !== null) {
            return new Set(JSON.parse(stored));
        }
    } catch (e) {
        // ignore malformed storage, fall through to defaults
    }
    // No saved preference yet - use the server-provided defaults (Won/Lost collapsed).
    const defaults = new Set();
    document.querySelectorAll('.kanban-column[data-default-collapsed="1"]').forEach(col => {
        defaults.add(col.dataset.status);
    });
    return defaults;
}

function saveCollapsedState(collapsedSet) {
    try {
        localStorage.setItem(COLLAPSE_STORAGE_KEY, JSON.stringify([...collapsedSet]));
    } catch (e) {
        // storage unavailable - collapse state just won't persist, not fatal
    }
}

function applyCollapsedState(collapsedSet) {
    document.querySelectorAll('.kanban-column-wrapper').forEach(wrapper => {
        const status = wrapper.dataset.statusWrapper;
        const column = wrapper.querySelector('.kanban-column');
        const icon = wrapper.querySelector('.kanban-toggle-icon');
        const isCollapsed = collapsedSet.has(status);

        column.style.display = isCollapsed ? 'none' : '';
        icon.style.transform = isCollapsed ? 'rotate(-90deg)' : '';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let collapsedColumns = loadCollapsedState();
    applyCollapsedState(collapsedColumns);

    // Toggle a column open/closed on header click, and remember the choice.
    document.querySelectorAll('.kanban-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const status = button.dataset.status;
            if (collapsedColumns.has(status)) {
                collapsedColumns.delete(status);
            } else {
                collapsedColumns.add(status);
            }
            saveCollapsedState(collapsedColumns);
            applyCollapsedState(collapsedColumns);
        });
    });

    // Live search across lead name + company name. While searching, temporarily
    // expand every column so matches in collapsed columns (e.g. Won/Lost) are
    // visible; clearing the search restores whatever was collapsed before.
    const searchInput = document.getElementById('kanban-search');
    let collapsedBeforeSearch = null;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();

        if (query && collapsedBeforeSearch === null) {
            collapsedBeforeSearch = new Set(collapsedColumns);
            applyCollapsedState(new Set()); // expand all columns while searching
        } else if (!query && collapsedBeforeSearch !== null) {
            applyCollapsedState(collapsedBeforeSearch);
            collapsedBeforeSearch = null;
        }

        document.querySelectorAll('.kanban-column').forEach(column => {
            let visibleCount = 0;

            column.querySelectorAll('.kanban-card').forEach(card => {
                const matches = !query
                    || card.dataset.name.includes(query)
                    || card.dataset.company.includes(query);

                card.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });

            const countBadge = document.querySelector(`[data-status-count="${column.dataset.status}"]`);
            if (countBadge) {
                countBadge.textContent = query
                    ? `${visibleCount}/${column.querySelectorAll('.kanban-card').length}`
                    : column.querySelectorAll('.kanban-card').length;
            }
        });
    });

    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            onEnd: (evt) => {
                const card = evt.item;
                const leadId = card.dataset.id;
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
                .then(data => {
                    // Swap the card's left-border color to match its new status, no reload needed
                    Object.values(statusBorderColors).forEach(cls => {
                        card.classList.remove(...cls.split(' '));
                    });
                    const newColorClass = statusBorderColors[newStatus];
                    if (newColorClass) {
                        card.classList.add(...newColorClass.split(' '));
                    }
                })
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