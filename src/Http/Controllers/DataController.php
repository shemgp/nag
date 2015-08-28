<?php

namespace DragonFly\Nag\Http\Controllers;

use Illuminate\Contracts\Http\Kernel;
use Validator;

class CheckController extends \App\Http\Controllers\Controller
{

	public function validate($request, $field, Kernel $kernel)
	{
		// Check if the form request is registered
		if (!isset( $kernel->formRequests[$request] ))
		{
			abort(404, 'The "' . $request . '" request is not registered for validation.');
		}

		// Load the rules and check if the field has rules
		$all_rules = (new $kernel->formRequest[$request]())->rules();

		if (!array_key_exists($field, $all_rules))
		{
			abort(404, 'There are no rules defined for"' . $field . '"');
		}

		$field_rules = $all_rules[$field];

		// Make sure the rules for this field are in array format
		if (!is_array($field_rules))
		{
			$field_rules = explode('|', $field_rules);
		}

		// Filter for DB validation rules
		$get_db_rules = function ($rule)
		{
			return starts_with($rule, ['unique', 'check']);
		};

		$db_rules = array_filter($field_rules, $get_db_rules);

		if (count($db_rules) == 0)
		{
			abort(404, 'No validation was done');
		}

		// Validate the value
		$validator = Validator::make([$field => Input::get($field)], [$db_rules]);

		if ($validator->fails())
		{
			abort(404, $validator->messages()->first($field));
		}

		return response()->json(['status' => 'ok']);
	}
}