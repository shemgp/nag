<?php

namespace DragonFly\Nag\Converters;

class Parsley extends Contract
{

    protected $date_format = 'YYYY-MM-DD';
    protected $trigger = null;

    protected function mapRule($field, $rule, $params, $message, $fieldType)
    {
        switch ($rule) {
            case 'accepted':
            case 'required':
                $parsleyRule = $rule;
                $params = '';
                break;

            case 'email':
                $parsleyRule = 'type';
                $params = 'email';
                break;

            case 'min':
                if ($fieldType == 'string') {
                    $parsleyRule = 'minlength';
                } else if ($fieldType == 'numeric') {
                    $parsleyRule = 'min';
                }

                $message = str_replace(':min', $params[0], $message);
                break;

            case 'max':
                if ($fieldType == 'string') {
                    $parsleyRule = 'maxlength';
                } else if ($fieldType == 'numeric') {
                    $parsleyRule = 'max';
                }

                $message = str_replace(':max', $params[0], $message);
                break;

            case 'between':
                $parsleyRule = 'length';
                $params = str_replace([':min', ':max'], $params, '[:min,:max]');
                $message = str_replace([':min', ':max'], $params, $message);
                break;

            case 'integer':
                $parsleyRule = 'integer';
                break;

            case 'numeric':
                $parsleyRule = 'digits';
                break;

            case 'url':
                $parsleyRule = 'type';
                $params = 'url';
                break;

            case 'alpha_num':
                $parsleyRule = 'alphanum';
                $params = '/^\d[a-zа-яё\-\_]+$/i';
                break;

            case 'alpha_dash':
                $parsleyRule = 'pattern';
                $params = '/^\d[a-zа-яё\-\_]+$/i';
                break;

            case 'alpha':
                $parsleyRule = 'pattern';
                $params = '/^[a-zа-яё]+$/i';
                break;

            case 'regex':
                $parsleyRule = 'pattern';
                break;

            case 'confirmed':
                $message = null;
                break;

            case 'same':
            case 'different':
                $parsleyRule = $rule;
                $message = str_replace(':other', $params[0], $message);
                $params = '#' . $this->getHtmlId($params[0]);
                break;

            case 'date_format':
                $parsleyRule = 'dateformat';
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
                $this->date_format = $params;
                $message = str_replace(':format', $params, $message);
                break;

            case 'before':
            case 'after':
                $parsleyRule = $rule;
                $params = $params[0];
                if ($this->date_format !== null) {
                    $params .= '|-|' . $this->date_format;
                }
                break;

            case 'in':
            case 'not_in':
                $parsleyRule = camel_case($rule) . 'List';
                $params = implode(',', $params);
                break;

            case 'exists':
            case 'unique':
                $route = $this->formRequest->getRoute();

                // Only register if the formRequest was registered in the kernel
                if ($route !== false) {
                    $parsleyRule = 'remote';
                    $params = route($route, [
                        'request' => $this->formRequest->kernel_key,
                        'field' => $field,
                    ]);
                }
                break;

            case 'active_url':
                $parsleyRule = 'activeUrl';
                $params = '#' . $this->getHtmlId($field);

                // Only trigger this validation on focusout
                $this->trigger = 'focusout';
                break;

            default:
                $parsleyRule = null;
                $message = null;
                break;
        }

        $attributes = [];

        if ($message) {
            if (is_array($params) && count($params) == 1) {
                $params = $params[0];
            }

            $attributes = [
                'data-parsley-' . $parsleyRule => $params,
                'data-parsley-' . $parsleyRule . '-message' => $message,
            ];

            if ($this->trigger != null) {
                $attributes['data-parsley-trigger'] = $this->trigger;
            }
        }

        return $attributes;
    }

    protected function confirmationRule($attribute, $message)
    {
        $attributes = [
            'data-parsley-equalto' => "#{$attribute}",
            'data-parsley-equalto-message' => $message
        ];
        return $attributes;
    }

}
