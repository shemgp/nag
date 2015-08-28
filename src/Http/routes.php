<?php

Route::get('validate-ui/{request}/{field}', [
    'uses' => '\DragonFlyAdmin\ValidateUI\Http\Controllers\CheckController@validate',
    'as' => 'ui.validate'
]);