<?php

namespace AntonioPrimera\WebPage\Tests\TestContext;

class BitManagerTester extends \AntonioPrimera\WebPage\Managers\BitManager
{
	
	public function exposeResolveBitDefinition($definition)
	{
		return $this->resolveBitDefinition($definition);
	}
	
	public function exposeUnpackRules(array $definition)
	{
		return $this->unpackRules($definition);
	}
	
	public function exposeNormalizeRules(array $definition)
	{
		return $this->normalizeRules($definition);
	}
}