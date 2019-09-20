<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Services;

class Loop extends Base
{
	public function getSubscription()
	{
		$rObj	= new \MTM\Async\Models\Subscriptions\Loop();
		$rObj->setParent($this);
		$this->addSubscription($rObj);
		return $rObj;
	}
}