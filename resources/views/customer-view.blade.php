<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $customer->Company_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $customer->Status ?? 'Active' }}
            </p>
        </div>

        <a href="#"
           class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">
            Edit
        </a>

    </div>

    <!-- Company Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <h2 class="font-semibold text-lg mb-4">
            Company Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <p class="text-sm text-gray-500">Company Name</p>
                <p>{{ $customer->Company_Name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p>{{ $customer->Company_Email }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p>{{ $customer->Company_No }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
                    @if($customer->Status == 'Active')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            Active
                        </span>

                    @elseif($customer->Status == 'Lead')
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            Lead
                        </span>

                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                            Inactive
                        </span>
                    @endif
            </div>
             <!-- Company Note -->
            <div>
                <p class="text-sm text-gray-500">Note</p>
                <p>{{ $customer->Company_Note }}</p>
            </div>

        </div>

    </div>

    </div>

</div>



  <!-- Company Lead -->
<div class="bg-white rounded-lg shadow p-6 mt-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            Related Leads
        </h2>

        <a href="{{ route('leads') }}"
           class="text-cyan-600 hover:text-cyan-700 text-sm">
            View All Leads
        </a>
    </div>

    @if($customer->leads->count())

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Lead Name</th>
                        <th class="px-4 py-3 text-left">Source</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Last Updated</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($customer->leads as $lead)

                    <tr>

                        <td class="px-4 py-3 font-medium">
                            {{ $lead->Lead_Name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $lead->Source }}
                        </td>

                        <td class="px-4 py-3">

                            @if($lead->Status == 'Won')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Won
                                </span>

                            @elseif($lead->Status == 'Qualified')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    Qualified
                                </span>

                            @elseif($lead->Status == 'Lost')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                    Lost
                                </span>

                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    {{ $lead->Status }}
                                </span>
                            @endif

                        </td>

                        <td class="px-4 py-3 text-gray-500">
                            {{ $lead->Updated_At }}
                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>
        </div>

    @else

        <div class="text-center py-8 text-gray-500">
            No leads linked to this customer.
        </div>

    @endif

</div>

    </div>

</div>

<div class ="bg-white rounded-lg shadow p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800">
    Activity Cards
    </h2>

</div>

@endsection