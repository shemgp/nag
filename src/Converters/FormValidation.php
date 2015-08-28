<?php

namespace DragonFly\Nag\Converters;


class FormValidation extends Contract
{

	protected function mapRule($field, $rule, $params, $message, $fieldType)
	{
		switch ($rule)
		{
			case 'accepted':
			case 'required':
				return [
					'data-fv-notempty'         => true,
					'data-fv-notempty-message' => $message,
				];
				break;

			case 'email':
				return [
					'data-fv-emailaddress'         => true,
					'data-fv-emailaddress-message' => $message,
				];
				break;

			case 'min':
				$message = str_replace(':min', $params[0], $message);
				if ($fieldType == 'string')
				{
					$attributes = [
						'data-fv-stringlength'         => true,
						'data-fv-stringlength-min'     => $params[0],
						'data-fv-stringlength-message' => $message,
					];
				}
				else if ($fieldType == 'numeric')
				{
					$attributes = [
						'data-fv-greaterthan'           => true,
						'data-fv-greaterthan-value'     => $params[0],
						'data-fv-greaterthan-inclusive' => true,
						'data-fv-greaterthan-message'   => $message,
					];
				}

				return $attributes;
				break;

			case 'max':
				$message = str_replace(':max', $params[0], $message);
				if ($fieldType == 'string')
				{
					$attributes = [
						'data-fv-stringlength'         => true,
						'data-fv-stringlength-max'     => $params[0],
						'data-fv-stringlength-message' => $message,
					];
				}
				else if ($fieldType == 'numeric')
				{
					$attributes = [
						'data-fv-lessthan'           => true,
						'data-fv-lessthan-value'     => $params[0],
						'data-fv-lessthan-inclusive' => true,
						'data-fv-lessthan-message'   => $message,
					];
				}

				return $attributes;
				break;

			case 'between':
				$params = str_replace([':min', ':max'], $params, '[:min,:max]');
				$message = str_replace([':min', ':max'], $params, $message);

				if ($fieldType == 'string')
				{
					$attributes = [
						'data-fv-stringlength'         => true,
						'data-fv-stringlength-min'     => $params[0],
						'data-fv-stringlength-max'     => $params[1],
						'data-fv-stringlength-message' => $message,
					];
				}
				else if ($fieldType == 'numeric')
				{
					$attributes = [
						'data-fv-lessthan'              => true,
						'data-fv-lessthan-value'        => $params[1],
						'data-fv-lessthan-inclusive'    => true,
						'data-fv-lessthan-message'      => $message,
						'data-fv-greaterthan'           => true,
						'data-fv-greaterthan-value'     => $params[0],
						'data-fv-greaterthan-inclusive' => true,
						'data-fv-greaterthan-message'   => $message,
					];
				}

				return $attributes;
				break;

			case 'integer':
				$attributes = [
					'data-fv-integer'         => true,
					'data-fv-integer-message' => $message,
				];

				return $attributes;
				break;

			case 'numeric':
				$attributes = [
					'data-fv-numeric'         => true,
					'data-fv-numeric-message' => $message,
				];

				return $attributes;
				break;

			case 'url':
				$attributes = [
					'data-fv-uri'         => true,
					'data-fv-uri-message' => $message,
				];

				return $attributes;
				break;

			case 'alpha_num':
				$params = '/^\d[a-zа-яё\-\_]+$/i';

				$attributes = [
					'data-fv-regexp'         => true,
					'data-fv-regexp-regexp'  => $params,
					'data-fv-regexp-message' => $message,
				];

				return $attributes;
				break;

			case 'alpha_dash':
				$params = '/^\d[a-zа-яё\-\_]+$/i';

				$attributes = [
					'data-fv-regexp'         => true,
					'data-fv-regexp-regexp'  => $params,
					'data-fv-regexp-message' => $message,
				];

				return $attributes;
				break;

			case 'alpha':
				$params = '/^[a-zа-яё]+$/i';

				$attributes = [
					'data-fv-regexp'         => true,
					'data-fv-regexp-regexp'  => $params,
					'data-fv-regexp-message' => $message,
				];

				return $attributes;
				break;

			case 'regex':
				$attributes = [
					'data-fv-regexp'         => true,
					'data-fv-regexp-regexp'  => $params,
					'data-fv-regexp-message' => $message,
				];

				return $attributes;
				break;

			case 'confirmed':
				$params = $field . '_confirmation';

				$attributes = [
					'data-fv-identical'         => true,
					'data-fv-identical-field'   => $params,
					'data-fv-identical-message' => $message,
				];

				return $attributes;
				break;

			case 'same':

				$message = str_replace(':other', $params[0], $message);
				$attributes = [
					'data-fv-identical'         => true,
					'data-fv-identical-field'   => $params[0],
					'data-fv-identical-message' => $message,
				];

				return $attributes;
				break;
			case 'different':
				$message = str_replace(':other', $params[0], $message);
				$attributes = [
					'data-fv-different'         => true,
					'data-fv-different-field'   => $params[0],
					'data-fv-different-message' => $message,
				];

				return $attributes;
				break;

			case 'date_format':
				$replace = [
					// Day
					'd' => 'DD', 'D' => 'ddd', 'j' => 'D', 'l' => 'DDDD',
					'N' => 'E', 'S' => '', 'w' => 'W', 'z' => 'DDD',
					// Week
					'W' => 'w',
					// Month
					'F' => 'MMMM', 'm' => 'MM', 'M' => 'MMM', 'n' => 'M', 't' => '',
					// Year
					'L' => '', 'o' => 'YYYY', 'Y' => 'YYYY', 'y' => 'YY',
					// Time
					'a' => 'a', 'A' => 'A', 'B' => '', 'g' => 'h', 'G' => 'H',
					'h' => 'hh', 'H' => 'HH', 'i' => 'i', 's' => 's', 'u' => '',
				];
				$params = str_replace(array_keys($replace), array_values($replace), $params[0]);
				$message = str_replace(':format', $params, $message);

				$attributes = [
					'data-fv-date'         => true,
					'data-fv-date-format'  => $params[0],
					'data-fv-date-message' => $message,
				];

				return $attributes;

				break;

			case 'before':
			case 'after':
				$placement = ( $rule == 'before' ) ? 'max' : 'min';
				$attributes = [
					'data-fv-date-' . $placement => $params[0],
				];

				return $attributes;
				break;

			case 'in':
			case 'not_in':
				$formValidationRule = str_replace('_', '', $rule);
				$params = implode(',', $params);

				$attributes = [
					'data-fv-' . $formValidationRule              => true,
					'data-fv-' . $formValidationRule . '-values'  => $params,
					'data-fv-' . $formValidationRule . '-message' => str_replace(':attribute', $field, $message),
				];

				return $attributes;
				break;

			case 'exists':
			case 'unique':
				$route = $this->formRequest->getRoute();

				// Only register if the formRequest was registered in the kernel
				if ($route !== false)
				{
					$params = route($route, [
						'request' => $this->formRequest->kernel_key,
						'field'   => $field,
					]);

					$attributes = [
						'data-fv-remote'      => true,
						'data-fv-remote-url'  => $params,
						'data-fv-remote-type' => 'GET',
						'data-fv-trigger'     => 'focus blur',
						'data-fv-verbose'     => false,
					];

					return $attributes;
				}
				break;

			case 'active_url':
				$attributes = [
					'data-fv-activeurl' => true,
					'data-fv-trigger'   => 'focus blur',
					'data-fv-verbose'   => false,
				];

				return $attributes;
				break;

			default:
				return null;
				break;
		}

	}
}
