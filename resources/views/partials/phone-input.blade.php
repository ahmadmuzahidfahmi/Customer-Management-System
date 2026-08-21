{{--
    Standardized phone number field — a searchable country-code picker (reusing
    the same floating picker used for Lead/Contact/Company/User fields) paired
    with a plain digits-only number input.

    Usage:
        @include('partials.phone-input', [
            'countryField' => 'Country_Code',
            'numberField' => 'Contact_No',
            'countries' => $countries,
            'selectedCode' => old('Country_Code', $contact->Country_Code ?? '+60'),
            'selectedNumber' => old('Contact_No', $contact->Contact_No ?? ''),
        ])
--}}
<div class="flex gap-2">

    <div class="w-32 shrink-0">
        @include('partials.entity-picker', [
            'fieldName' => $countryField,
            'options' => collect($countries)->map(fn ($c) => ['id' => $c['code'], 'label' => $c['code'], 'sublabel' => $c['name']]),
            'placeholder' => 'Code',
            'title' => 'Select Country Code',
            'selectedId' => $selectedCode ?? '+60',
        ])
    </div>

    <input
        type="text"
        name="{{ $numberField }}"
        value="{{ $selectedNumber ?? '' }}"
        inputmode="numeric"
        placeholder="123456789"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        class="flex-1 border rounded-lg px-3 py-2">

</div>
