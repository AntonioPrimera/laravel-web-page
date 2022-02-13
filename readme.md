# Your new package name

## Installation

## Usage

## Architecture

The web page uses Components and Bits to be able to store any configurable data. Components
are used to group other components and bits. Bits are used to store texts, image urls or
any other data which will be displayed.

### Components

Components are Eloquent Models, 

### Bits


### Creating a recipe (via Artisan)

To create a new recipe file, you can use the following artisan command:

`php artisan web-page:make:recipe RecipeName`

This will generate a simple recipe file, something like a database migration, where you can define
the list of components and bits to be created at root level and their component and bit structures.

If you want to create a complex recipe, defining the components, aliases and bit aliases, you can
add the flag `--complex` to the above artisan command.

### Creating / deleting components using a recipe file

Once you have created a recipe file, with the necessary components and bits for your web page,
you can use another artisan command to actually create the Web Components defined in the file as
Eloquent Models in the database:

`php artisan web-page:recipe RecipeName`

This will use the recipe file `RecipeName` and run its `up()` method to create the structure you returned
from the `recipe()` method.

If you want to undo the creation of components from a recipe file, you can use the `--down` flag
on the above command, which will look for all created Web Components and Bits and remove them
(something like the 'migrate:rollback' command). This basically just runs the `down()` method of the
recipe, which, if not overridden, will blindly try to remove all Eloquent Models created by the `up()`
method recursively.

### ComponentManager

#### Creating a component

Method signature:

`createComponent(string $description, array $definition = []): ?Component`

The description format is as follows: "**type:name:uid**". The type is mandatory, but the
name and the uid are optional. If not provided, the uid will be the component name in
kebab case. If not provided, the name will be the component type.

Examples:

- "Page:Home:home-page" will create a component of type "Page", with the name "Home"
and the uid "home-page"
- "Page:Home" will create a component of type "Page", with the name "Home" and the uid "home"
- "Page" will create a component of type "Page", with the name "Page" and the uid "page"

The types must be described in the config: **webComponents.components**. Each component type
must have the type as the key and an array with the component definition as the value. If a
component must contain other components the list of components will be provided as an indexed
array. If a component must contain a set of bits, the list of bits will be provided as an
indexed array.

Config sample (webComponents.php):

```php
    return [
        'components' => [
            'Page' => [],
            
            'Section' => [],
            
            'Link' => [
                'bits' => [
                    'Label', 'Url'
                ]
            ],
            
            'Picture' => [
                'bits' => [
                    'Source', 'Label'
                ]
            ],
        ],
        
        'bits' => [
            'ShortText' => [
                'editor'   => 'input#text',				//default editor, so it can be omitted
                'rules'    => ['string', 'max:255'],
            ],
            
            'LongText' => [
                'editor' => 'textarea',
                'rules'  => ['string'],					//default rule, so it can be omitted
            ],
            
            //short text aliases
            'Label' => 'alias:ShortText',
            'Title' => 'alias:ShortText|required',
            'Url'   => 'alias:ShortText|rules:url',
            
            //used for file uploads by the admin
            'File' => [
                'editor' => 'input#file',
            ],
        ],
    ];
```


#### Creating a bit

Method signature:

`createBit(string $description): ?Bit`

The description format is as follows: "**type:name:uid**". The type is mandatory, but the
name and the uid are optional. If not provided, the uid will be the bit name in
kebab case. If not provided, the name will be the bit type.

Examples:

- "Title:MainTitle:main-title" will create a Bit of type "Title", with the name "MainTitle"
  and the uid "main-title"
- "Title:PageTitle" will create a Bit of type "Title", with the name "PageTitle" and 
  the uid "page-title"
- "Title" will create a Bit of type "Title", with the name "Title" and the uid "title"



### Helper

webPage()->getLanguage()

### Facade

WebPage::getLanguage()