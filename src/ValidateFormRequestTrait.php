<?php

namespace DragonFly\Nag;

/**
 * Class ValidateFormRequestTrait
 * @package DragonFlyAdmin\ValidateUI
 */
trait ValidateFormRequestTrait
{

	/**
	 * Returns the name of the route, through which the validation request should be sent.
	 *
	 * If it's not set on the object it will load the global route.
	 *
	 * @return string|false
	 */
	public function getRoute()
	{
		if ($this->kernel_key == null)
		{
			return false;
		}

		return ( property_exists($this, 'route') ) ?
			$this->route : config('validateui.route');
	}

    public function getKernelKey()
    {
        return (property_exists($this, 'kernel_key')) ? $this->kernel_key : false;
    }
}