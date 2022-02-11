<?php
namespace AntonioPrimera\WebPage\Managers;

use AntonioPrimera\WebPage\Facades\ComponentDictionary;
use AntonioPrimera\WebPage\Managers\Traits\CleansUp;
use AntonioPrimera\WebPage\Managers\Traits\HandlesBitInstances;
use AntonioPrimera\WebPage\Managers\Traits\CreatesComponents;
use AntonioPrimera\WebPage\Managers\Traits\RetrievesComponents;
use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Models\WebComponent;
use AntonioPrimera\WebPage\WebPage;
use Illuminate\Support\Str;

use BadFunctionCallException;

class ComponentManager
{
	use CreatesComponents,
		RetrievesComponents,
		HandlesBitInstances,
		CleansUp;
	
	/**
	 * The owner of this ComponentManager instance
	 * (who's components are we managing)
	 *
	 * @var WebPage|WebComponent|null
	 */
	protected WebPage | WebComponent | null $owner;
	
	public function __construct($owner = null)
	{
		$this->setOwner($owner);
	}
	
	/**
	 * Creates a component or a bit, from a description string. It will create a component
	 * only if the type is defined, otherwise it will create a bit.
	 * The description format:
	 * 		"<type>:<name>:<uid>" - type is mandatory, the name and uid can be inferred
	 */
	public function create(string $description): WebComponent | Bit | null
	{
		$type = $this->decomposeItemDescription($description)['type'];
		
		//if the type is not a pre-defined component type, then a bit is created
		return ComponentDictionary::isDefined($type)
			? $this->createComponent($description, ComponentDictionary::getDefinition($type))
			: $this->createBit($description);
	}
	
	//--- Magic stuff -------------------------------------------------------------------------------------------------
	
	/**
	 * Handles method calls with the following pattern: create<Type>($name = null, $uid = null)
	 * and forwards them to $this->create($type, $name, $uid)
	 * Like the underlying "create" method, this will create a component if the type is pre-defined in the
	 * 'webComponents' config, otherwise it will create a bit
	 *
	 *
	 * e.g. createSection('Header', 'page-header')
	 * 		will create a component of type 'Section', name 'Header' and uid 'page-header'
	 *
	 * 		createSomeUndefinedStuff()
	 * 		will create a bit of type 'SomeUndefinedStuff', name 'SomeUndefinedStuff', uid 'some-undefined-stuff'
	 * 		because there is no pre-defined component type "SomeUndefinedStuff"
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return Bit|WebComponent|null
	 */
	public function __call(string $name, array $arguments)
	{
		$methodName = Str::of($name);
		
		if ($methodName->startsWith('create')) {
			
			$itemType = $methodName->replaceFirst('create', '');
			$itemName = ($arguments[0] ?? null);
			$itemUid = ($arguments[1] ?? null);
			
			return $this->create(implode(':', array_filter([$itemType, $itemName, $itemUid])));
		}
	
		throw new BadFunctionCallException("[AP-LWP][ComponentManager] Unknown method: $name");
	}
	
	//--- Component retrieval -----------------------------------------------------------------------------------------
	
	/**
	 * Get the component or attribute at a given dot separated uid path, optionally
	 * followed by a dot separated attribute path. The component path is
	 * separated from the attribute path by a colon ":"
	 *
	 * Format: dot.separated.component.uid.path:bit-uid#language
	 * (the language and the bit-uid are optional)
	 *
	 * e.g. $componentManager->get('homepage.header.image')
	 * 		will get the image component under the header component, under the homepage component
	 *
	 * e.g. $componentManager->get('homepage.header.image:url')
	 * 		will get the url Bit instance from the image component
	 *
	 * e.g. $componentManager->get('homepage.header.image:url#')
	 * 		will get the url data (string) for the default language from the image component
	 *
	 * e.g. $componentManager->get('homepage.header.image:url#en')
	 * 		will get the url data (string) for language 'en' from the nested image component
	 *
	 *
	 * @param string $path
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get(string $path, mixed $default = null): mixed
	{
		//split the path into a component path and an attribute path
		$pathParts = explode(':', $path, 2);
		$uidPath = $pathParts[0];
		$bitPath = $pathParts[1] ?? null;
		
		//search for the component
		$component = $this->getComponent($uidPath);
		
		//if we have a valid component and a bit path, get the bit / bitData
		if ($component && $bitPath) {
			//the bit path signature: <bit-uid>#<language>
			$bitPathParts = explode('#', $bitPath, 2);
			$bitUid = $bitPathParts[0];
			$language = $bitPathParts[1] ?? null;
			
			$bit = $component->componentManager()->getBit($bitUid);
			
			return $bit && $language !== null
				? $bit->getBitData($language, $default)
				: ($bit ?: $default);
		}
		
		return $component ?: $default;
	}
	
	//--- Getters and setters -----------------------------------------------------------------------------------------
	
	public function getOwner(): WebPage | WebComponent | null
	{
		return $this->owner;
	}
	
	public function setOwner(WebPage | WebComponent | null $owner)
	{
		$this->owner = $owner;
		return $this;
	}
}