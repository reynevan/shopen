<div style="display: inline-block; width: 45%; vertical-align: top;" class="text-sm">
    <div class="semibold">Nabywca:</div>
    @if ($address->first_name || $address->last_name)
        <div>{{ $address->first_name }} {{ $address->last_name }}</div>
    @endif
    @if ($address->company)
        <div>{{ $address->company }}</div>
    @endif
    @if ($address->nip)
        <div>NIP: {{ $address->nip }}</div>
    @endif
    <div>{{ $address->postal_code }} {{ $address->city }}</div>
    <div>{{ $address->address_line }}</div>
    @if ($address->phone)
        <div>tel.: {{ $address->phone }}</div>
    @endif
</div>