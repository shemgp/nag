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
			$message = 'The "' . $request . '" request is not registered for validation.';

			// Parsley response
			if (config('nag.driver') == 'Parsley')
			{
				abort(404, $message);
			}

			// FormValidation response
			return response()->json(['valid' => false, 'message' => $message]);

		}

		// Load the rules and check if the field has rules
		$all_rules = (new $kernel->formRequest[$request]())->rules();

		if (!array_key_exists($field, $all_rules))
		{
			$message = 'There are no rules defined for"' . $field . '"';

			// Parsley response
			if (config('nag.driver') == 'Parsley')
			{
				abort(404, $message);
			}

			// FormValidation response
			return response()->json(['valid' => false, 'message' => $message]);

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
			$message = 'No validation was done';

			// Parsley response
			if (config('nag.driver') == 'Parsley')
			{
				abort(404, $message);
			}

			// FormValidation response
			return response()->json(['valid' => false, 'message' => $message]);
		}

		// Validate the value
		$validator = Validator::make([$field => Input::get($field)], [$db_rules]);

		if ($validator->fails())
		{
			$message = $validator->messages()->first($field);

			if (config('nag.driver') == 'Parsley')
			{
				abort(404, $message);
			}

			// FormValidation response
			return response()->json(['valid' => false, 'message' => $message]);
		}

		// Parsley response
		if (config('nag.driver') == 'Parsley')
		{
			return response()->json(['status' => 'ok']);
		}

		// FormValidation response
		return response()->json(['valid' => true]);
	}
}