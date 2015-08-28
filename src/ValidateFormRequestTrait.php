<?php

namespace DragonFly\Nag;

/**
 * Class ValidateFormRequestTrait
 * @package DragonFlyAdmin\ValidateUI
 */
trait ValidateFormRequestTrait
{
	public $kernel_key = null;
	public $route      = null;

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

		return ( $this->route != null ) ?
			$this->route : config('validateui.route');
	}
}