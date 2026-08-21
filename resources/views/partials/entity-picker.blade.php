{{--
    Reusable searchable picker — a floating modal for selecting one item out of a
    potentially long list (leads, contacts, companies, users), styled after the
    "Link Existing Contact" modal on the customer view page.

    Usage:
        @include('partials.entity-picker', [
            'fieldName' => 'Lead_ID',
            'options' => $leads->map(fn ($l) => ['id' => $l->Lead_ID, 'label' => $l->Lead_Name, 'sublabel' => $l->company->Company_Name ?? null]),
            'placeholder' => 'Link to Lead...',
            'title' => 'Select a Lead',
            'selectedId' => old('Lead_ID'), // optional
        ])
--}}
<div x-data="{
        open: false,
        search: '',
        selectedId: @js((string) ($selectedId ?? '')),
        options: @js(collect($options)->values()),
        placeholderText: @js($placeholder ?? 'Select...'),
        get selectedLabel() {
            const found = this.options.find(o => String(o.id) === String(this.selectedId));
            return found ? found.label : '';
        },
        get filtered() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o =>
                o.label.toLowerCase().includes(q) ||
                (o.sublabel ?? '').toLowerCase().includes(q)
            );
        },
        pick(o) {
            this.selectedId = o.id;
            this.open = false;
            this.search = '';
        }
     }"
     class="relative">

    <input type="hidden" name="{{ $fieldName }}" x-model="selectedId">

    <button type="button" @click="open = true; $nextTick(() => $refs.search.focus())"
            class="w-full border rounded-lg px-3 py-2 pr-8 text-sm text-left bg-white hover:bg-gray-50">
        <span :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'" x-text="selectedLabel || placeholderText"></span>
    </button>

    <button type="button" x-show="selectedId" x-cloak
            @click.stop="selectedId = ''"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs">
        ✕
    </button>

    <div x-show="open" x-cloak
         @click.self="open = false"
         @keydown.escape.window="open = false"
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Select' }}</h3>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            <input type="text" x-model="search" x-ref="search" placeholder="Search..."
                   class="w-full border rounded-lg px-3 py-2 text-sm mb-3">

            <div class="overflow-y-auto space-y-1 flex-1">
                <template x-for="o in filtered" :key="o.id">
                    <button type="button" @click="pick(o)"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm flex justify-between items-center">
                        <span class="font-medium text-gray-800" x-text="o.label"></span>
                        <span class="text-gray-400 text-xs" x-text="o.sublabel ?? ''"></span>
                    </button>
                </template>
                <p x-show="filtered.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
            </div>
        </div>
    </div>
</div>
