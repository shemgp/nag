<?php

namespace DragonFly\Nag;


use DragonFly\Nag\Facade as ValidationConverter;

trait ValidateFormTrait
{
	/**
	 * @type ValidationConverter
	 */
	protected $converter = null;

	/**
	 * {@inheritdoc}
	 */
	public function open(array $options = [])
	{
		$this->reserved[] = 'request';
		$this->converter = ValidationConverter::init(array_get($options, 'request', null));

        $options = array_merge($this->converter->formOptions, $options);

		return parent::open($options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function input($type, $name, $value = null, $options = [])
	{
		if ($this->converter != null)
		{
			$options = array_merge($options, $this->converter->retrieveRules($name));
		}

		return parent::input($type, $name, $value, $options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function textarea($name, $value = null, $options = [])
	{
		if ($this->converter != null)
		{
			$options = array_merge($options, $this->converter->retrieveRules($name));
		}

		return parent::textarea($name, $value, $options);
	}

	public function select($name, $list = [], $selected = null, $options = [])
	{
		if ($this->converter != null)
		{
			$options = array_merge($options, $this->converter->retrieveRules($name));
		}

		return parent::select($name, $list, $selected, $options);
	}
}