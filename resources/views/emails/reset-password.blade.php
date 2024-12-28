<x-mail::message>
# Reset Password

Hi, {{$name}}

We received a request to reset your password. If you made this request, click the button below to reset your password:  

[**Reset Password**]({{$link}})

If you did not request to reset your password, please ignore this email. Your password will remain unchanged.  

If you have any questions or need further assistance, please contact our support team.  

Stay secure,<br>
{{ config('app.name') }}
</x-mail::message>
