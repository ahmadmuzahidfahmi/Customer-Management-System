<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management System</title>

    <link rel="icon" type="image/png" 
        href="{{ asset('image/visivest Logo.png') }}">
   
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body
    class="bg-gray-100"x-data="{ mobileMenu: false, sidebarOpen: true }">

<nav class="bg-[rgb(70,192,189)] text-white shadow-lg">

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

        <!-- Right -->
        <a href="{{ route('profile') }}"
           class="flex items-center gap-3 hover-grow">

            <div class="text-right hidden sm:block">
                <p class="font-semibold">Admin</p>
                <p class="text-xs text-indigo-200">
                    System Manager
                </p>
            </div>

            <div
                class="w-10 h-10 rounded-full bg-white text-indigo-700 flex items-center justify-center font-bold">
                A
            </div>

        </a>

    </div>

</nav>

<div class="flex min-h-screen">

    <!-- Sidebar -->
<aside
    :class="sidebarOpen ? 'w-72 translate-x-0' : 'w-0 -translate-x-full md:translate-x-0'"
    class="fixed md:relative top-0 left-0 h-full md:h-auto md:self-stretch bg-white shadow-lg z-[50] transition-all duration-300 overflow-hidden">

    <div class="flex flex-col h-full w-72">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-5 border-b">
            <h2 class="font-bold text-gray-800">Navigation</h2>
            <button @click="sidebarOpen = false" class="text-gray-600 text-xl md:hidden">✕</button>
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

            <a href="{{ route('contacts') }}"
               class="px-4 py-3 rounded-lg
               {{ request()->routeIs('contacts') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                Contacts
            </a>

            <a href="{{ route('leads') }}"
               class="px-4 py-3 rounded-lg
               {{ request()->routeIs('leads') ? 'bg-cyan-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                Leads
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
</body>
</html>
</nav>






