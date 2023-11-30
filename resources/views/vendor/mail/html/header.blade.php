<tr style="background: #e6eefd">
    <td class="header">
        <a href="https://farenow.com" style="display: inline-block; padding-top: 15px">
            @if (trim($slot) === 'Farenow')
                <img src="{{ config('app.url') . '/api/logo' }}" class="logo" alt="Farenow">
            @else
                {{ $slot }}
            @endif
        </a>
    </td>
</tr>
