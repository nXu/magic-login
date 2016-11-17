# Magic Login for Laravel

## Contents
- What's this?
  - Summary
  - How does Magic Login work?
- Installation
- Usage
- Security
- License

## What's this?
### Summary
This is a basic proof-of-concept implementation of a passwordless login 
method for Laravel applications.
  
### How does Magic Login work?
The Magic Login protocol is based on the TOTP algorithm defined in 
[RFC 6238][totp]. This is commonly used for two-factor authentication 
with the Google Authenticator app.

![Schema][schema]

The basic process is as follows: 

1. The website assigns a cryptographically secure random token for the user  
when the user enables the magic login.

2. The app saves the token of the user.

3. On the login screens, the website displays a QR code containing the 
login URL including a unique id used to identify the login request.
The site maintains a WebSocket connection with the server.

4. The user scans the QR code using the Magic Login app.
 
5. The app calculates a HMAC signature based on the current time using
the token of the user as key.

6. The app sends a login request to the scanned login URL. 

7. The server validates the login request and sends a one-time authentication 
token to the client website using WebSocket.

8. The client authenticates itself using the one-time authentication token.

8. Login is successful.

## Requirements
- Laravel 5
- Pusher (other push drivers, maybe Laravel Echo are planned)

## Install
To install Magic Login for laravel, use composer:

`composer require nxu/magic-login`

Then add the following line to the providers array in the `app.php` config file:
`Nxu\MagicLogin\MagicLoginServiceProvider::class,`

## Configuration
To configure Magic Login, you can publish the config file using 
`artisan vendor:publish`. Then you can edit `config/magiclogin.php`.

## Usage
The service provider automatically defines the route used to verify and 
broadcast login requests. For the customization of the route, refer to the
configuration chapter above.

All you need to do is to display the following QR codes:

- Magic Login token of the user to be saved in the mobile application
- Login URL on the login page

## Security
If you discover a vulnerability in either the protocol or the implementation,
please open a GitHub issue or contact me at nxu@nxu.hu. 
 
## License
The project is open-sourced software licensed under the [MIT license][mit].

[totp]: https://tools.ietf.org/html/rfc6238
[schema]: http://i.imgur.com/DoL7uot.png
[mit]: https://opensource.org/licenses/MIT

