<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">  

    <link rel="icon" type="image/png" href="{{ asset('image/visivest Logo.png') }}">
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
<div class="flex-1 max-w-xl mx-6 hidden md:flex items-center gap-2"
     x-data="{ filterOpen: false }">

    @if(request()->routeIs('recycle-bin'))
        {{-- No <form>, no reload — binds straight to the Alpine store --}}
        <div class="flex-1 relative">
            <input
                type="text"
                x-model="$store.search.query"
                placeholder="Search recycle bin..."
                class="w-full rounded-lg pl-10 pr-3 py-2 text-gray-800 bg-white/95 focus:outline-none focus:ring-2 focus:ring-white">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </div>
    @else
        <form method="GET" action="{{ url()->current() }}" class="flex-1 relative">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="@if(request()->routeIs('customers'))Search customers by name, status...@elseif(request()->routeIs('leads'))Search leads by name, source...@elseif(request()->routeIs('contacts'))Search contacts by name, email...@endif"
                class="w-full rounded-lg pl-10 pr-3 py-2 text-gray-800 bg-white/95 focus:outline-none focus:ring-2 focus:ring-white">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        </form>
    @endif

    <div  class="relative flex items-center gap-2">
        <button
            @click="filterOpen = !filterOpen"
            type="button"
            class="flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
            ⚙️ Filters
        </button>

        @if(request()->routeIs('recycle-bin'))
            <a href="#" @click.prevent="$store.search.query = ''"
               class="flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                ✕ Reset
            </a>
        @else
            <a href="{{ url()->current() }}"
               class="flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                ✕ Reset
            </a>
        @endif

        <div
            x-show="filterOpen"
            @click.away="filterOpen = false"
            x-cloak
            class="absolute right-0 top-full mt-2 w-64 bg-white text-gray-800 rounded-lg shadow-lg p-4 z-50 space-y-2">

            @if(request()->routeIs('customers'))
                <p class="text-sm text-gray-500">Customer filters (status, industry...) — coming soon</p>
            @elseif(request()->routeIs('leads'))
                <p class="text-sm text-gray-500">Lead filters (source, status...) — coming soon</p>
            @elseif(request()->routeIs('contacts'))
                <p class="text-sm text-gray-500">Contact filters (role, company...) — coming soon</p>
            @elseif(request()->routeIs('recycle-bin'))
                <p class="text-sm text-gray-500">No filters for this page</p>
            @else
                <p class="text-sm text-gray-500">No filters for this page</p>
            @endif

        </div>
    </div>

</div>
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

<!-- Leads -->
<div>
    <a href="{{ route('leads') }}"
       class="block px-4 py-3 rounded-lg
       {{ request()->routeIs('leads') || request()->routeIs('leads.kanban') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
        Leads
    </a>

    @if(request()->routeIs('leads') || request()->routeIs('leads.kanban'))
    <div class="ml-4 mt-1 space-y-1">
        <a href="{{ route('leads') }}"
           class="block px-4 py-2 rounded-lg text-sm
           {{ request()->routeIs('leads') ? 'text-cyan-600 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Table View
        </a>
        <a href="{{ route('leads.kanban') }}"
           class="block px-4 py-2 rounded-lg text-sm
           {{ request()->routeIs('leads.kanban') ? 'text-cyan-600 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
            Board View
        </a>
    </div>
    @endif
</div>

<!-- Activities -->
<a href="{{ route('activities.index') }}"
   class="px-4 py-3 rounded-lg
   {{ request()->routeIs('activities*') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
    Activities
</a>

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






