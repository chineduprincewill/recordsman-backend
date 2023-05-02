<!doctype html>
<html lang="en">
    <body>
        <div>
            <h1>Account Creation</h1>
            <p>Welcome! {{ $user->lastname }} {{ $user->firstname }}</p>
            <p>You received this email because you have been created on the RIMZ platform as <strong>{{ $user->role }}</strong>
            <p>Your login detail is</p>
            <p>Email : <strong>{{ $user->email }}</strong></p>
            <p>Password :  <strong>{{ $password }}</strong></p>
            <p>Click this <a href="https://imaginative-jalebi-d7716d.netlify.app/login" class="button button-primary" target="_blank">link</a> to login</p>
            <p>
                If at anytime you feel that your password has been compromised, you can click the link below to go to the forgot password page to reset your password.
            </p>
            <p>
                <a href="https://imaginative-jalebi-d7716d.netlify.app/forgot-password" class="button button-primary" target="_blank">Reset Password</a>
            </p>
            <p>Or paste the link <strong><i>https://imaginative-jalebi-d7716d.netlify.app/forgot-password</i></strong> on your browser to reset your password</p>
            <p>Regards,</p>
            <h1>RIMZ</h1>
        </div>
    </body>
</html>