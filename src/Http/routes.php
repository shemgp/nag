<?php

Route::get('validate-ui/{request}/{field}', [
    'uses' => '\DragonFly\Nag\Http\Controllers\DataController@validateInput',
    'as' => 'ui.validate'
]);