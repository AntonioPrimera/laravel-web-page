<?php

namespace AntonioPrimera\WebPage\Definitions;

abstract class Dictionary
{
	protected array $definitions = [];
	protected array $aliases = [];
	
	public function __construct()
	{
		$this->setup();
	}
	
	//--- Abstract methods --------------------------------------------------------------------------------------------
	
	public abstract function defaultDefinition(): array;
	
	protected abstract function setup();
	
	//--- Public methods ----------------------------------------------------------------------------------------------
	
	public function loadDefinitions(?array $definitions)
	{
		if ($definitions)
			$this->definitions = array_merge($this->definitions, $definitions);
	}
	
	public function loadAliases(?array $aliases)
	{
		if ($aliases)
			$this->aliases = array_merge($this->aliases, $aliases);
	}
	
	/**
	 * Get the definition instance for a given bit type
	 */
	public function getDefinition(string $type): array
	{
		$definition = $this->definitions[$type] ?? $this->resolveAlias($type);
		return array_merge($this->defaultDefinition(), $definition);
	}
	
	/**
	 * Check whether a given type is defined, either
	 * as a base type, or as an alias.
	 */
	public function isDefined(string $type): bool
	{
		return isset($this->definitions[$type]) || isset($this->aliases[$type]);
	}
	
	//--- Protected helpers -------------------------------------------------------------------------------------------
	
	protected function loadDictionaryFromConfig($configKey)
	{
		$this->loadDefinitions(config("$configKey.definitions"));
		$this->loadAliases(config("$configKey.aliases"));
	}
	
	/**
	 * Given an aliased bit type, return the aliased bit definition.
	 * If a definition is aliased, it is just returned, if it
	 * points to an alias, it is recursively resolved
	 */
	protected function resolveAlias($aliasedType)
	{
		//search in the bit definitions
		if (isset($this->definitions[$aliasedType]))
			return $this->definitions[$aliasedType];
		
		//if this is an alias to another alias, recursively resolve that alias
		if (isset($this->aliases[$aliasedType]))
			return $this->resolveAlias($this->aliases[$aliasedType]);
		
		//if no definition or alias was found, just return the default definition
		return $this->defaultDefinition();
	}
}