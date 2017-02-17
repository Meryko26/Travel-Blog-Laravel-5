# Laravel 5.4 CRUD API Android

- Website http://ruslan-website.com/laravel/travel_blog/

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/Snapshot.PNG)

## Android App

Download here: http://ruslan-website.com/laravel/travel_blog/apk/TravelBlog.apk

Source code: https://github.com/atabegruslan/Travel-Blog-Android

Use guest credentials to log in:

| Field | Value |
| --- | --- |
| User email | guest@guest.com |
| Password | gggggg |

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Android/master/Screenshot.png)

## API

- Get Token: POST `http://ruslan-website.com/laravel/travel_blog/oauth/token`

| Post Form Data Name | Post Form Data Value |
| --- | --- |
| client_id | (from oauth_clients table) |
| client_secret | (from oauth_clients table) |
| grant_type | password |
| username | (user email) |
| password | (user password) |

Return access token

- Get user data: GET `http://ruslan-website.com/laravel/travel_blog/api/user`

| Header Field Name | Header Field Value |
| --- | --- |
| Accept | application/json |
| Authorization | Bearer (access token) |

Return user data

- Get post entry: GET http://ruslan-website.com/laravel/travel_blog/api/entry

| Header Field Name | Header Field Value |
| --- | --- |
| Accept | application/json |
| Authorization | Bearer (access token) |

Return json entry(s)

- Post new entry: POST http://ruslan-website.com/laravel/travel_blog/api/entry

| Header Field Name | Header Field Value |
| --- | --- |
| Accept | application/json |
| Authorization | Bearer (access token) |

| Post Form Data Name | Post Form Data Value |
| --- | --- |
| user_id | (user id) |
| place | (place name) |
| comments | (comments) |
| image | (image) |

Return ok or error message

# How to make this app

## Make New Project

In CLI: `composer create-project --prefer-dist laravel/laravel travel_blog`

.env : 
```
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=***
```

config/database.php : 
```
'mysql' => [
	...
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	...
],
```

## Auth (Web)

Create default database tables for user: `php artisan migrate`

Make route, controller and model for user: `php artisan make:auth`

## Customize flow of app to the following diagram

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/flow.PNG)

New Controller for Entry: `php artisan make:controller EntryController --resource`

To ensure that Login must happen before CRUD Entry, add to EntryController:
```php
public function __construct(){
	$this->middleware('auth');
}
```

New Entry Model: `php artisan make:model Entry`

routes/web.php :
```php
Route::resource('/entry', 'EntryController');
```

Create: resources/views/entry.blade.php

In EntryController:
```php
public function index(){
	return view('entry');
}
```

In app/http/controllers/auth/LoginController, RegisterController & ResetPasswordController: `protected $redirectTo = '/entry';`


\app\Http\Middleware\RedirectIfAuthenticated.php: 
```php
if (Auth::guard($guard)->check()) {
	return redirect('/');
}
```

Delete home controller, view and route

Make new database table like below:

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/db.PNG)

## Text and Image MultiPart Upload Form

composer.json:
```js
"require": {
	"laravelcollective/html": "^5.3.0"
},
```
	
In CLI: `composer update`

config/app.php:
```php
'providers' => [
	Collective\Html\HtmlServiceProvider::class,
],
'aliases' => [
	'Form' => Collective\Html\FormFacade::class,
	'Html' => Collective\Html\HtmlFacade::class,
],
```

Good Tutorials:
- https://laravelcollective.com/docs/5.3/html
- https://laracasts.com/discuss/channels/general-discussion/errorexception-in-urlgeneratorphp-line-273)
- http://tutsnare.com/upload-files-in-laravel/
- http://itsolutionstuff.com/post/laravel-5-fileimage-upload-example-with-validationexample.html

## Auth for RESTful API

In CLI: `composer require laravel/passport`
  
config/app.php
```php
'providers' => [
	Laravel\Passport\PassportServiceProvider::class,
],
```

In CLI: `php artisan migrate`, `php artisan passport:install`

App\User (model):
```php
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable{
    use HasApiTokens, Notifiable;
}
```

App\Providers\AuthServiceProvider:
```php
use Laravel\Passport\Passport;
public function boot(){
	$this->registerPolicies();
	Passport::routes();
}
```

config/auth.php:
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

Good Tutorial: https://www.sitepoint.com/build-rest-resources-laravel/

## RESTful API

api.php: 
```php
Route::middleware('auth:api')->resource('/entry', 'EntryApiController');
```

In CLI: `php artisan make:controller EntryApiController --resource`, `php artisan make:model EntryApi`

## Search Box Functionality

routes/web.php:
```php
Route::get('/search', 'SearchController@index');
```

New Controller for Search: `php artisan make:controller SearchController`

Good Tutorials:
- https://tutorialedge.net/laravel-5-simple-site-search-bar
- http://anytch.com/laravel-5-simple-get-search/

## Facebook Login (Socialite)

In Facebook Developer Console:

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/fb_dev_con_1.PNG)

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/fb_dev_con_2.PNG)

In config/services.php

```php
'facebook' => [
    'client_id' => env('***'),
    'client_secret' => env('***'),
    'redirect' => env('***'),
]
```

In CLI : `composer require laravel/socialite`

In config/app.php

```php
'providers' => [
	Laravel\Socialite\SocialiteServiceProvider::class,
],
'aliases' => [
	'Socialite' => Laravel\Socialite\Facades\Socialite::class,
],
```

Add new columns in database's users table for social login

![](https://raw.githubusercontent.com/atabegruslan/Travel-Blog-Laravel-5/master/Illustrations/new_social_db_cols.PNG)

In app/User.php model

```php
class User extends Authenticatable{
...
    protected $fillable = [
        'name', 'email', 'password', 'type', 'social_id'
    ];
```

Add Facebook button to resources/views/auth/login.blade.php

```html
<a href="{{ route('social.login', ['facebook']) }}">
    <img src="fb.png">
</a> 
```

In routes.web.php

```php
Route::group(['middleware' => ['web']], function(){
	Route::get('auth/{provider}', ['uses' => 'Auth\AuthController@redirectToProvider', 'as' => 'social.login']);
	Route::get('auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback');
});
```

In App/Http/Controllers/Auth/AuthController.php

```php
public function redirectToProvider()
{
	return Socialite::driver('facebook')->redirect();
}
public function handleProviderCallback()
{
	$user = Socialite::driver('facebook')->user();
	$data = [
        'name' => $user->getName(),
        'email' => $user->getEmail(),
        'type' => 'facebook',
        'social_id' => $user->getId(),
        'password' => ''
    ];
    Auth::login(User::firstOrCreate($data));
    return Redirect::to('/entry');
}
```

Useful tutorials:

https://github.com/laravel/socialite

https://www.youtube.com/watch?v=D3oLLz8bFp0

http://devartisans.com/articles/complete-laravel5-socialite-tuorial

## Contact form with emailing ability



## Upload to server

- public folder to server's public folder
- The rest to another folder outside of the server's public folder
- public/index.php: rectify all relevant paths
- import .sql to server's database, rectify database-name, username & password in the .env file
