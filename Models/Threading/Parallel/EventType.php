<?php
//© 2019 Martin Peter Madsen
namespace MTM\Async\Models\Threading\Parallel;

class EventType extends Base
{
	protected $_typeId=null;
	
	public function __construct($typeId)
	{
		$this->_typeId	= $typeId;
	}
	public function getTypeId()
	{
		return $this->_typeId;
	}
}