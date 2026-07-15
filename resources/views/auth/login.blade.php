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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-[rgb(70,192,189)] focus:ring-[rgb(70,192,189)] px-3 py-2">
                </div>

                <button
                    type="submit"
                    class="w-full bg-[rgb(70,192,189)] hover:opacity-90 text-white font-semibold rounded-lg py-2 transition-colors">
                    Sign in
                </button>
            </form>
        </div>
    </div>

</body>
</html>