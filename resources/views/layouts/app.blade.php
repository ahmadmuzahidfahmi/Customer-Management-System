<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management System</title>

    <link rel="icon" type="image/png" 
        href="{{ asset('image/visivest Logo.png') }}">
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100" x-data="{ mobileMenu: false }">

<nav class="bg-[rgb(70,192,189)] text-white shadow-lg sticky top-0 z-50">

    <div class="w-full px-6 py-4">

        <!-- Row 1 -->
        <div class="flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <img
                    src="{{ asset('image/Visivest Logo_White.png') }}"
                    alt="Visivest Logo"
                    class="h-16 sm:h-20 md:h-24 lg:h-32 w-auto">

            </div>

                    <!-- Navigation -->
        <div class="hidden md:flex items-center space-x-8">
            <a href="{{ route('dashboard') }}"
            class="text-white pb-1 {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('customers') }}"
            class="text-white pb-1 {{ request()->routeIs('customers') ? 'border-b-2 border-white' : '' }}">
                Customers
            </a>

            <a href="{{ route('leads') }}"
            class="text-white pb-1 {{ request()->routeIs('leads') ? 'border-b-2 border-white' : '' }}">
                Leads
            </a>

        </div>

            <!-- Profile -->
<a href="{{ route('profile') }}" class="flex items-center gap-3 hover-grow">
            <div class="text-right hidden sm:block">
                <p class="font-semibold">Admin</p>
                <p class="text-xs text-indigo-200">System Manager</p>
            </div>

            <div
                class="w-10 h-10 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold shadow">
                A
            </div>
        </a>


            </div>

        </div>
   
    </div>
</nav>

<nav class="">
<!-- Row 2 -->
        <div class="md:hidden mt-4">
            <button
                @click="mobileMenu = true"
                class="flex items-center gap-2 px-3 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition">

                <span class="text-lg">☰</span>
                <span>Menu</span>

            </button>
        </div>
</body>
</html>
</nav>
<!-- Mobile Sidebar Overlay -->
<div
    x-show="mobileMenu"
    x-transition.opacity
    @click="mobileMenu = false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[55] md:hidden"    style="display:none;">
</div>

    <!-- Page content -->
    <main class="p-6">
        @yield('content')
    </main>

<!-- Mobile Sidebar -->
<div 
    x-show="mobileMenu"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed top-0 left-0 h-full w-72 bg-white/70 shadow-xl z-[60] md:hidden" style="display:none;">

    <!-- Sidebar Header -->
    <div class="flex justify-between items-center p-5 border-b">

        <h2 class="font-bold text-gray-800">
            Navigation
        </h2>

        <button
            @click="mobileMenu = false"
            class="text-gray-600 text-xl">
            ✕
        </button>

    </div>

    <!-- Links -->
    <div class="flex flex-col p-4 space-y-2">

        <a href="{{ route('dashboard') }}"
           class="px-4 py-3 rounded-lg
           {{ request()->routeIs('dashboard') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            Dashboard
        </a>

        <a href="{{ route('customers') }}"
           class="px-4 py-3 rounded-lg
           {{ request()->routeIs('customers') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            Customers
        </a>

        <a href="{{ route('leads') }}"
           class="px-4 py-3 rounded-lg
           {{ request()->routeIs('leads') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            Leads
        </a>

    </div>

</div>

    <!-- Footer -->
    <footer class="bg-gray-200 text-gray-600 text-sm text-center py-4 mt-10">
        <p>&copy; {{ date('Y') }} Week 2.0. All rights reserved.</p>
    </footer>

</body>
</html>
