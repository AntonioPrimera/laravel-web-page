<?php

namespace AntonioPrimera\WebPage\Managers;

use AntonioPrimera\WebPage\Managers\Traits\HandlesBitInstances;
//use AntonioPrimera\WebPage\Managers\Traits\HandlesComponentAttributes;
use AntonioPrimera\WebPage\Managers\Traits\HandlesComponentInstances;
//use AntonioPrimera\WebPage\Models\Bit;
use AntonioPrimera\WebPage\Managers\Traits\ManagerHelpers;
use AntonioPrimera\WebPage\Models\Component;
use AntonioPrimera\WebPage\WebPage;
use Illuminate\Support\Str;

use BadFunctionCallException;
//use Illuminate\Support\Str;
//use Illuminate\Support\Stringable;
//use JetBrains\PhpStorm\ArrayShape;

/**
 *
 */
class ComponentManager
{
	use HandlesComponentInstances, HandlesBitInstances, ManagerHelpers;	//HandlesComponentAttributes,
	
	////predefined component types
	//const TYPE_COLLECTION 	= 'Collection';
	
	const COMPONENT  = 'Component';
	const BIT		 = 'Bit';
	const COLLECTION = 'Collection';
	
	/**
	 * The owner of this ComponentManager instance
	 * (who's components are we managing)
	 *
	 * @var WebPage|Component|null
	 */
	protected WebPage | Component | null $owner;
	
	public function __construct($owner = null)
	{
		$this->setOwner($owner);
	}
	
	public function create(string $description)
	{
		return $this->createComponent($description)
			?: $this->createBit($description);
		
		////destructure the description (format '<type>:<name>:<uid>' - only the type is mandatory)
		//[$type, $name, $uid] = $this->itemDescription($description);
		//
		//foreach (config('webComponents.components', []) as $cType => $definition)
		//	if ($type === $cType)
		//		return $this->createComponent($type, $name, $uid, $definition);
		//
		//foreach (config('webComponents.bits', []) as $bType => $definition) {
		//	if ($type === $bType)
		//		return $this->createBit($type, $name, $uid);
		//}
		//
		//return null;
	}
	
	//--- Magic stuff -------------------------------------------------------------------------------------------------
	
	public function __call(string $name, array $arguments)
	{
		$methodName = Str::of($name);
		
		if ($methodName->startsWith('create')) {
			
			$itemType = $methodName->replaceFirst('create', '');
			$itemName = ($arguments[0] ?? $itemType);
			$itemUid = ($arguments[1] ?? null);
			
			return $this->create("$itemType:$itemName:$itemUid");
		}
		//$createMethod = $this->decomposeMagicCreationMethod($name);
		//if ($createMethod) {
		//	return ($createMethod['bit'] ?? true)
		//		? $this->createBit($createMethod['type'], $createMethod['name'], $arguments[0] ?? null)
		//		: $this->createComponent($createMethod['type'], $createMethod['name'], $arguments[0] ?? null);
		//}
	
		throw new BadFunctionCallException("[AP-LWP][ComponentManager] Unknown method: $name");
	}
	
	//--- Component retrieval -----------------------------------------------------------------------------------------
	
	/**
	 * Get the component or attribute at a given dot separated uid path, optionally
	 * followed by a dot separated attribute path. The component path is
	 * separated from the attribute path by a colon ":"
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
	
	public function getOwner(): WebPage | Component | null
	{
		return $this->owner;
	}
	
	public function setOwner(WebPage | Component | null $owner)
	{
		$this->owner = $owner;
		return $this;
	}
	
	///**
	// * Decomposes method names starting with "create". The method looks for the following 2 formats, in this order:
	// * 	1. "create<ComponentName><ComponentType>" - ComponentType must be one of: ['Page', 'Section', 'Collection']
	// *  2. "create<BitName><PreConfiguredBitType>" - the bit types are configured in config file: webComponents.php
	// *
	// * For the first format, the ComponentType is removed from the end of the name.
	// * e.g. "createHomePagePage" 		 	>> ['type' => 'Page', 'name' => 'HomePage']
	// * 		"createHomePage" 		 		>> ['type' => 'Page', 'name' => 'Home']
	// * 		"createCtaSection" 				>> ['type' => 'Section', 'name' => 'Cta']
	// * 		"createPicturesCollection", 	>> ['type' => 'Collection', 'name' => 'Pictures']
	// *
	// * If the method name doesn't end in a predefined component type, the component type Bit is assumed, and the
	// * second template is assumed to be used. In this case, the bit type is not removed from the component name.
	// * This is done to be able to use method names like "createTitle" instead of "createTitleTitle".
	// * e.g.	"createHomepage"				>> ['type' => 'Bit', 'name' => 'Homepage', 'definition' => $this->defaultBitDefinition()]
	// * 		"createTitle"					>> ['type' => 'Bit', 'name' => 'Title', 'definition' => config('webComponents.bits.Title')]
	// * 		"createCtaLink"					>> ['type' => 'Bit', 'name' => 'CtaLink', 'definition' => config('webComponents.bits.Link')]
	// * 		"createHeaderImage"				>> ['type' => 'Bit', 'name' => 'HeaderImage', 'definition' => config('webComponents.bits.Image')]
	// *
	// * @param $name
	// *
	// * @return array|null
	// */
	//#[ArrayShape(['bit' => 'bool', 'type' => 'string', 'name' => 'string'])]
	//protected function decomposeMagicCreationMethod($name): ?array
	//{
	//	$methodName = Str::of($name);
	//
	//	//only create methods are decomposed
	//	if (!$methodName->startsWith('create'))
	//		return null;
	//
	//	$methodName = $methodName->replaceFirst('create', '');
	//
	//	return $this->decomposeComponentMethodName($methodName)
	//		?: $this->decomposeBitMethodName($methodName)
	//		?: [
	//			'bit'  => true,
	//			'type' => config('webComponents.defaultBit'),
	//			'name' => $methodName,
	//		];
	//}
	//
	//protected function decomposeComponentMethodName(string $method)
	//{
	//	$methodName = Str::of($method);
	//
	//	//if the method name ends with Component, this must be a component
	//	if ($methodName->endsWith('Component')) {
	//		$methodName = $methodName->replaceLast('Component', '');
	//
	//		return [
	//			'bit' => false,
	//			'type' => $methodName,
	//			'name' => $methodName
	//		];
	//	}
	//
	//	//go through all configured component types - config('webComponents.components')
	//	foreach ($this->getDefinedComponentTypes() as $type) {
	//		if ($methodName->endsWith($type))
	//			return [
	//				'bit'  => false,
	//				'type' => $type,
	//				'name' => $methodName->replaceLast($type, ''),
	//			];
	//	}
	//
	//	return null;
	//}
	//
	//protected function decomposeBitMethodName(string $method)
	//{
	//	$methodName = Str::of($method);
	//	$mustBeBit = $methodName->endsWith('Bit');
	//
	//	//if the method name ends with Bit, a Bit creation is required
	//	if ($mustBeBit)
	//		$methodName->replaceLast('Bit', '');
	//
	//	//go through all configured bit types - config('webComponents.bits')
	//	foreach (config('webComponents.bits') as $bitType => $bitFields) {
	//		if ($methodName->endsWith($bitType))
	//			return [
	//				'bit'		 => true,
	//				'type' 		 => $bitType,
	//				'name'		 => $methodName,
	//			];
	//	}
	//
	//	return $mustBeBit
	//		? [
	//			'bit'  => true,
	//			'type' => config('webComponents.defaultBit'),
	//			'name' => $methodName,
	//		]
	//		: null;
	//}
	//
	///**
	// * Returns a list of component types, which should be automatically recognized
	// * as pre-defined. This is useful in the magic creation method names.
	// * Bit component types are handled separately.
	// *
	// * e.g. ['Page', 'Section', 'Collection']
	// *
	// * @return string[]
	// */
	//protected function getDefinedComponentTypes()
	//{
	//	$configuredTypes = array_keys(config('webComponents.components', []));
	//	$predefinedTypes = [
	//		static::TYPE_COLLECTION,
	//	];
	//
	//	return array_merge($configuredTypes, $predefinedTypes);
	//}
	
	/**
	 * Method Name								>>> Type / Name / Uid
	 * ---------------------------------------------------------------
	 * createCtaComponent()						>>> Cta / Cta / cta
	 * createCta		  						>>> Cta / Cta / cta
	 * createHomePage	  						>>> Page / HomePage / home-page
	 * createHome('Page')						>>> Page / Home / home
	 * createHome('Page', 'home-page')			>>> Page / Home / home-page
	 * createGallery('Section')
	 */
}