@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Oops!')
@else
# @lang('Password Reset!')
@endif
@endif

{{-- Intro Lines --}}
Anda lupa dengan password Anda? Untuk mereset password anda silahkan klik tombol di bawah ini

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
Jika Anda tidak merasa melupakan password Anda, mohon untuk mengabaikan email ini. <br>
Note: Tautan ini akan kadaluarsa dalam {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} Menit

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Hormat Kami'),<br> <b>IS-ITD Team</b>
@endif

@endcomponent
