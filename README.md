ValidationUI
===============

Converts [FormRequest](http://laravel.com/docs/5.0/validation#form-request-validation) rules so you can validate forms in the front-end.

At the time of writing both [Parsley](http://parsleyjs.org/) and [formvalidation.io](http://formvalidation.io/) are supported.


## Install

Start by pulling in the package through composer:

    composer require dragonfly/nag
    
If you have previously set up `LaravelCollective/Html` or `Illuminate/Html` you can remove its service provider from `app/config`

in `app/config` add the following under service providers: 

`DragonFly\Nag\NagServiceProvider`

If you haven't already, add these facades:

```php
    'Form' => 'Collective\Html\FormFacade',
    'Html' => 'Collective\Html\HtmlFacade',
```

Open up `app/Http/Kernel.php`, add a public `$formRequest` property and assign it an empty array.

```php
    /**
     * Register form requests that require database validation (e.g. the ones that use the exists or unique rule).
     */
    public $formRequest = [];
```
    
Lastly do a `vendor:publish` to get the assets.

    php artisan vendor:publish --provider="DragonFly\Nag\NagServiceProvider"

## Getting started

By default `Nag` uses the `FormValidation` converter for your rules, if You'd like to change that to `Parsley` open up `config/nag.php` and change the `driver` key to `'Parsley'`.

It's important to note that both `Parsley` and `FormValidation` need additional validators,
if you did the vendor publish they should be located in `public/assets/js`.

Please include the required file after the plugin file.

**Note** The date validators require [moment.js](momentjs.com)

## Useage


All that's needed is for you to supply the name of the `FormRequest` (either by class name or fully namespaced) in the `request` key when opening a form:

```php
    Form::open(['request' => 'YourFormRequestClass'])
    Form::open(['request' => 'App\Http\Requests\YourFormRequestClass'])
    Form::model($model, ['request' => 'YourFormRequestClass'])
    Form::model($model, ['request' => 'App\Http\Requests\YourFormRequestClass'])
```

Lastly you should include parsley's scripts on the page and activate parsley for your form.

easy enough don't you think?

### Using database validation

Laravel ships with 2 database validation rules: `unique`, `exists`, these 2 rules need to send a request in order to be validated in the front end.

First you'll need to register the `FormRequest` in your `app/Http/Kernel.php`. For example if let's register a `RegisterUser` request (where we would check that the username should be unique).

We'll add it to `$formRequest`, the key you register it under will become the route's slug:

```php
    public $formRequest = [
        'user-register' => 'App\Http\Requests\RegisterUser',
    ];
```

next up we'll need to add the `ValidateFormRequestTrait` trait to the `RegisterUser` request and assign the same key we provided in our previous step to `$kernel_key`.

```php
<?php namespace App\Http\Requests;

use DragonFlyAdmin\ValidateUi\ValidateFormRequestTrait;

class RegisterUser extends Request {

    // Add the trait
	use ValidateFormRequestTrait;

    // Define the kernel key
    public $kernel_key = 'user-register';
    
    // Optionally set the route the validation request should be sent through (null for default)
    public $route = null;
    
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'username' => 'required|unique:users|min:3|max:32',
            'password' => 'required|min:5',
		];
	}

}

```

**Note** This requires parsley-remote to be loaded before Parsley
**Note** If no `$kernel_key` is defined the validation rule will be ignored in the front-end

#### Custom routing

You can define your own route on a per-`FormRequest` or global basis if you'd like to have more control over the request that's sent when validating database rules.

This is the default route that comes bundled:

```php
    Route::get('validate-ui/{request}/{field}', [
        'uses' => '\DragonFly\Nag\Http\Controllers\CheckController@validate',
        'as' => 'ui.validate'
    ]);
```

Your custom route should:
 * contain the 2 route parameters:  `{request}` & `{field}`
 * use the `\DragonFly\Nag\Http\Controllers\CheckController@validate`
 * should be named
 
Those are the only requirements, you can change the url to whatever you'd like and add any middleware.

You can replace the default route by editing `config/validateui.php`'s `route` key and set it to the route you'd prefer.

Next to that you are also able to set the route on your `FormRequest`, if you've attached the `ValidateFormRequestTrait` you should be able to assign your route name on the `$route` property.

### Assigning field ids

By default every form input uses the field's name as id, which is fine if you only display 1 form on a page and you're sure there are
no other elements on the page with the same id.

However, if you need more control over your form input's id's you can do so on your form `Request` class by adding a `map_html_ids`method.

This method should return an array, just as the rules, the key would be the name of the input, the value would be the id that will be used when rendering it.

```php
public function map_html_ids() {
    return [
        'first_name' => 'prefixed-first_name',
        'last_name' => 'first_name-suffixed'
    ];
}
```

if you want a standard format you could always loop over the rules' keys and format accordingly

```php
public function map_html_ids() {
    $fields = array_keys($this->rules());
    
    $format = function($name){
        return 'new-format-' . $name;
    };
    
    $ids = array_map($format, $fields);
    
    return array_combine($fields, $ids);
}
```

### Validation rules

Implemented |                 |            |                 | Not implemented
------------|-----------------|------------|-----------------|---------------
 required   | check           | confirmed  | same            | ip_address
 email      | unique          | regex      | active_url      | mimes
 min        | date_format     | alpha      | numeric         | image
 max        | before          | alpha_dash | different       | array
 between    | after           | alpha_num  | in              |
 integer    | accepted        | url        | not_in          |