<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">  

    <link rel="icon" type="image/png" href="{{ asset('Image/Visivest Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body
    class="bg-gray-100"
    x-data="{ mobileMenu: false, sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true') }"
    x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', JSON.stringify(value)))">

<nav class="sticky top-0 z-40 bg-[rgb(70,192,189)] text-white shadow-lg">
    <div class="flex items-center justify-between px-6 py-3">

        <!-- Left -->
        <div class="flex items-center gap-4">

            <button
                @click="sidebarOpen = !sidebarOpen"
                class="hidden md:block text-2xl">
                ☰
            </button>

            <img
                src="{{ asset('image/Visivest Logo_White.png') }}"
                alt="Logo"
                class="h-12 w-auto">

        </div>

        <!-- Middle - search bar -->

@unless(request()->routeIs('dashboard'))

@php
    $hasSearch = request()->routeIs([
        'customers',
        'contacts',
        'leads',
        'leads.kanban',
        'activities.index',
        'audit-log',
        'recycle-bin',
    ]);

    $hasFilters = request()->routeIs([
        'customers',
        'contacts',
        'leads',
        'activities.index',
        'audit-log',
    ]);
@endphp

@if($hasSearch || $hasFilters)

<div
    class="flex-1 max-w-xl mx-6 hidden md:flex items-center gap-2"
    x-data="{ filterOpen: false }">

    @if($hasSearch)

        @if(request()->routeIs('recycle-bin'))

            <div class="flex-1 relative">
                <input
                    type="text"
                    x-model="$store.search.query"
                    placeholder="Search recycle bin..."
                    class="w-full rounded-lg pl-10 pr-3 py-2 text-gray-800 bg-white/95 focus:outline-none focus:ring-2 focus:ring-white">

                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    🔍
                </span>
            </div>

        @else

            <form
                id="global-search-form"
                method="GET"
                action="{{ url()->current() }}"
                class="flex-1 relative">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search..."
                    class="w-full rounded-lg pl-10 pr-3 py-2 text-gray-800 bg-white/95 focus:outline-none focus:ring-2 focus:ring-white">

                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    🔍
                </span>

            </form>

        @endif

    @endif

<div
        class="relative flex items-center gap-2">

        @if($hasFilters)
            <button
                @click="filterOpen = !filterOpen"
                type="button"
                class="flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                ⚙️ Filters
            </button>
        @endif

        @if(request()->anyFilled(['search', 'status', 'type', 'source', 'sort', 'mine', 'action']))
            <a href="{{ url()->current() }}"
               class="flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                ✕ Reset
            </a>
        @endif

        @if($hasFilters)
            <div
                x-show="filterOpen"
                @click.away="filterOpen = false"
                x-cloak
                class="absolute right-0 top-full mt-2 w-64 bg-white text-gray-800 rounded-lg shadow-lg p-4 z-50 space-y-2">

                @if(request()->routeIs('customers'))
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All statuses</option>
                                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Lead" {{ request('status') === 'Lead' ? 'selected' : '' }}>Lead</option>
                                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sort By</label>
                            <select name="sort" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A–Z</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z–A</option>
                            </select>
                        </div>
                    </div>

                @elseif(request()->routeIs('leads'))
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All statuses</option>
                                <option value="New" {{ request('status') === 'New' ? 'selected' : '' }}>New</option>
                                <option value="Contacted" {{ request('status') === 'Contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="Qualified" {{ request('status') === 'Qualified' ? 'selected' : '' }}>Qualified</option>
                                <option value="Won" {{ request('status') === 'Won' ? 'selected' : '' }}>Won</option>
                                <option value="Lost" {{ request('status') === 'Lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Source</label>
                            <select name="source" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All sources</option>
                                @foreach($sources ?? [] as $sourceOption)
                                    <option value="{{ $sourceOption }}" {{ request('source') === $sourceOption ? 'selected' : '' }}>
                                        {{ $sourceOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                @elseif(request()->routeIs('contacts'))
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sort By</label>
                        <select name="sort" form="global-search-form" onchange="this.form.submit()"
                                class="w-full border rounded-lg px-2 py-1.5 text-sm">
                            <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A–Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z–A</option>
                            <option value="company_asc" {{ request('sort') === 'company_asc' ? 'selected' : '' }}>Company A–Z</option>
                            <option value="company_desc" {{ request('sort') === 'company_desc' ? 'selected' : '' }}>Company Z–A</option>
                        </select>
                    </div>

                @elseif(request()->routeIs('activities.index'))
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All</option>
                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select name="type" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All</option>
                                @foreach(['Call', 'Email', 'Meeting', 'Follow-Up', 'Other'] as $type)
                                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="mine" value="1" form="global-search-form"
                                   onchange="this.form.submit()" {{ request('mine') ? 'checked' : '' }}>
                            My activities only
                        </label>
                    </div>

                @elseif(request()->routeIs('audit-log'))
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                            <select name="action" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All Actions</option>
                                @foreach(['created','updated','deleted','restored','force_deleted','viewed','login','logout'] as $action)
                                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ',$action)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                            <select name="type" form="global-search-form" onchange="this.form.submit()"
                                    class="w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="">All Types</option>
                                @foreach(['Customer','Contact','Lead','Note','Auth','Page'] as $type)
                                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                @endif

            </div>
        @endif

    </div>

</div>

@endif

@endunless

    <!-- Right -->
<div class="flex items-center gap-3">

    <a href="{{ route('profile') }}" class="flex items-center gap-3 hover-grow">
        <div class="text-right hidden sm:block">
            <p class="font-semibold">{{ auth()->user()->User_Name }}</p>
            <p class="text-xs text-indigo-200">{{ auth()->user()->User_Role }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold">
            {{ strtoupper(substr(auth()->user()->User_Name, 0, 1)) }}
        </div>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
            Logout
        </button>
    </form>

</div>

</div>

    </div>

</nav>

@if(auth()->check() && auth()->user()->User_Role === 'Guest')
    <div class="bg-amber-50 border-b border-amber-200 text-amber-800 text-sm px-6 py-2 text-center">
        You're browsing as a guest — read-only mode. Sign in with a full account to make changes.
    </div>
@endif

@if(session('guest_blocked'))
    <div class="bg-red-50 border-b border-red-200 text-red-700 text-sm px-6 py-2 text-center">
        {{ session('guest_blocked') }}
    </div>
@endif

<div class="flex min-h-screen">

    <!-- Sidebar -->
<aside
    :class="sidebarOpen ? 'w-72 translate-x-0' : 'w-0 -translate-x-full md:translate-x-0'"
    class="fixed md:sticky top-0 md:top-[72px] left-0 h-full md:h-[calc(100vh-72px)] bg-white shadow-lg z-[50] transition-all duration-300 overflow-hidden">

    <div class="flex flex-col h-full w-72 overflow-y-auto">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-5 border-b">
            <h2 class="font-bold text-gray-800">Navigation</h2>
            <button @click="sidebarOpen = false" class="text-gray-600 text-xl md:hidden">✕</button>
        </div>

        <!-- Links -->
        <div class="flex flex-col p-4 space-y-1">

            <a href="{{ route('dashboard') }}"
               class="px-4 py-3 rounded-lg
               {{ request()->routeIs('dashboard') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                Dashboard
            </a>

<!-- Customers -->
<a href="{{ route('customers') }}"
   class="px-4 py-3 rounded-lg
   {{ request()->routeIs('customers*') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
    Customers
</a>

<!-- Contacts -->
<a href="{{ route('contacts') }}"
   class="px-4 py-3 rounded-lg
   {{ request()->routeIs('contacts*') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
    Contacts
</a>

<div x-data="{
    activeMenu:
        @if(request()->routeIs('leads') || request()->routeIs('leads.kanban'))
            'leads'
        @elseif(request()->routeIs('activities*') || request()->routeIs('audit-log') || request()->routeIs('calendar'))
            'activities'
        @else
            null
        @endif
}">
<!-- Leads -->
<div>
    <!-- Main Leads Nav -->
    <a href="{{ route('leads') }}"
       @click="activeMenu = 'leads'"
       class="flex items-center justify-between px-4 py-3 rounded-lg
       {{ request()->routeIs('leads') || request()->routeIs('leads.kanban')
            ? 'bg-cyan-500 text-white'
            : 'text-gray-700 hover:bg-gray-100' }}">

        <span>Leads</span>

        <svg class="w-4 h-4 transition-transform"
            :class="{ 'rotate-180': activeMenu === 'leads' }"
            fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </a>

    
    <!-- Dropdown -->
    <div x-show="activeMenu === 'leads'"
         x-transition
         class="ml-4 mt-2 space-y-1">

        <!-- Table View -->
        <a href="{{ route('leads') }}"
           class="block px-4 py-2 rounded-lg text-sm
           {{ request()->routeIs('leads')
                ? 'bg-cyan-100 text-cyan-700 font-medium'
                : 'text-gray-600 hover:bg-gray-100' }}">
            Table View
        </a>

        <!-- Board View -->
        <a href="{{ route('leads.kanban') }}"
           class="block px-4 py-2 rounded-lg text-sm
           {{ request()->routeIs('leads.kanban')
                ? 'bg-cyan-100 text-cyan-700 font-medium'
                : 'text-gray-600 hover:bg-gray-100' }}">
            Board View
        </a>

    </div>

</div>


<!-- Activities -->
<div>
    <!-- Main Activities Nav -->
    <a href="{{ route('activities.index') }}"
       @click="activeMenu = 'activities'"
       class="flex items-center justify-between px-4 py-3 rounded-lg
       {{ request()->routeIs('activities*') || request()->routeIs('audit-log') || request()->routeIs('calendar')
            ? 'bg-cyan-500 text-white'
            : 'text-gray-700 hover:bg-gray-100' }}">

        <span>Activities</span>

        <svg class="w-4 h-4 transition-transform"
            :class="{ 'rotate-180': activeMenu === 'activities' }"           
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </a>

    <!-- Dropdown Menu -->
    <div x-show="activeMenu === 'activities'"
         x-transition
         class="ml-4 mt-2 space-y-1">

        <a href="{{ route('activities.index') }}"
           class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('activities*')
                ? 'bg-cyan-100 text-cyan-700'
                : 'text-gray-600 hover:bg-gray-100' }}">
            Activities
        </a>

        @if(auth()->check() && auth()->user()->User_Role === 'Admin')
        <a href="{{ route('audit-log') }}"
           class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('audit-log')
                ? 'bg-cyan-100 text-cyan-700'
                : 'text-gray-600 hover:bg-gray-100' }}">
            Audit Log
        </a>
        @endif

        <a href="{{ route('calendar') }}"
           class="block px-4 py-2 rounded-lg
           {{ request()->routeIs('calendar')
                ? 'bg-cyan-100 text-cyan-700'
                : 'text-gray-600 hover:bg-gray-100' }}">
            Calendar
        </a>

    </div>

</div>
</div>

<!-- Recyle bin -->
<a href="{{ route('recycle-bin') }}"
    class="px-4 py-3 rounded-lg
    {{ request()->routeIs('recycle-bin') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
    Recycle Bin
</a>

        </div>
    </div>

</aside>

        <!-- Page content -->
<main class="flex-1 p-6 overflow-x-auto">

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</div>


<nav class="">
<!-- Row 2 -->
        <div class="md:hidden mt-4">
<button
    @click="mobileMenu = true"
    class="fixed bottom-6 right-6 z-50
           bg-cyan-600 text-white
           w-14 h-14 rounded-full
           shadow-lg md:hidden">

    ☰

</button>
        </div>
            @stack('scripts')
</body>
</html>
</nav>