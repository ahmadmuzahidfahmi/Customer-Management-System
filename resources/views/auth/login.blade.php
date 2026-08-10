<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Customer Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('image/visivest Logo.png') }}" alt="Logo" class="h-14 w-auto">
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-xl font-bold text-gray-800 mb-6 text-center">Sign in</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input
                        type="text"
                        name="User_Name"
                        value="{{ old('User_Name') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border-gray-300 focus:border-[rgb(70,192,189)] focus:ring-[rgb(70,192,189)] px-3 py-2">
                </div>

<div x-data="{ showPassword: false }">

    <label class="block text-sm font-medium text-gray-700 mb-1">
        Password
    </label>

    <div class="relative">

        <input
            :type="showPassword ? 'text' : 'password'"
            name="password"
            required
            class="w-full rounded-lg border-gray-300
                   focus:border-[rgb(70,192,189)]
                   focus:ring-[rgb(70,192,189)]
                   px-3 py-2 pr-10">

<button
    type="button"
    @click="showPassword = !showPassword"
    x-data="{ tooltip: false }"
    @mouseenter="tooltip = true"
    @mouseleave="tooltip = false"
    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">

    <!-- Eye -->
    <svg
        x-show="!showPassword"
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">

        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0
                 3 3 0 016 0zm2.458 0C16.732 15.057
                 14.157 17 12 17
                 s-4.732-1.943-5.458-5
                 C7.268 8.943 9.843 7
                 12 7s4.732 1.943
                 5.458 5z"/>
    </svg>

    <!-- Eye Off -->
    <svg
        x-show="showPassword"
        x-cloak
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor">

        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13.875 18.825A10.05 10.05 0 0112 19
                 c-4.478 0-8.268-2.943-9.542-7
                 a9.956 9.956 0 012.442-3.568
                 M6.223 6.223A9.953 9.953 0 0112 5
                 c4.478 0 8.268 2.943 9.542 7
                 a9.964 9.964 0 01-4.293 5.774
                 M15 12a3 3 0 00-3-3
                 m0 0a3 3 0 00-2.12.879
                 M3 3l18 18"/>
    </svg>

    <!-- Tooltip -->
    <div
        x-show="tooltip"
        x-transition
        x-cloak
        class="absolute right-0 top-full mt-2
               bg-gray-800 text-white text-xs
               px-2 py-1 rounded whitespace-nowrap">

        <span x-text="showPassword ? 'Hide Password' : 'Show Password'"></span>

    </div>

</button>
    </div>

</div>

                <button
                    type="submit"
                    class="w-full bg-[rgb(70,192,189)] hover:opacity-90 text-white font-semibold rounded-lg py-2 transition-colors">
                    Sign in
                </button>
            </form>
            <div class="flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400 uppercase tracking-wide">or</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <p class="text-sm text-gray-500 text-center mt-4 mb-4">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-[rgb(70,192,189)] hover:underline font-medium">Register</a>
            </p>

            <form method="POST" action="{{ route('login.guest') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg py-2 transition-colors">
                    Continue as Guest
                </button>
            </form>
            <p class="text-xs text-gray-400 text-center mt-2">Guest access is read-only — you can browse, but not create, edit, or delete records.</p>
        </div>
    </div>

</body>
</html>