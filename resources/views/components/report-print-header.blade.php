@props([
    'company' => null,
])

@php
    $printCompany = $company ?? session('company');
    $brandName = trim((string) (optional($printCompany)->name ?: 'Company Assist'));
    $brandWords = preg_split('/\s+/', $brandName) ?: [];
    $brandTitle = $brandWords[0] ?? 'Company';
    $brandSub = trim(implode(' ', array_slice($brandWords, 1)));
    $address = optional($printCompany)->address;
    $contact = optional($printCompany)->contact;
    $email = optional($printCompany)->email;
@endphp

<div {{ $attributes->merge(['class' => 'invHeaderTop']) }}>
    <h1>{{ $brandTitle }}</h1>
    @if ($brandSub !== '')
        <h4>{{ $brandSub }}</h4>
    @endif
    @if (filled($address))
        <p class="locInfo">{{ $address }}</p>
    @endif
    @if (filled($contact) || filled($email))
        <p class="contactInfo">
            @if (filled($contact)){{ $contact }}@endif
            @if (filled($contact) && filled($email)), @endif
            @if (filled($email)){{ $email }}@endif
        </p>
    @endif
</div>
