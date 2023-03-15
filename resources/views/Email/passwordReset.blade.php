@component('mail::message')
# Change password

Click on the button below to change password.

@component('mail::button', ['url' => config('app.app_ui_uri').'/reset_password?token='.$token])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
