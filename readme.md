# Your new package name

## Installation

Install the package via composer:

`composer require antonioprimera/laravel-web-page`

Then run the migrations via `php artisan migrate`, in order to create the WebComponents table
`lwp_components` and the WebBits table `lwp_bits`.

That's it, you can now use web components and web bits in your project.

## Usage

You can create WebComponents and WebBits using recipe files, maintain their data using the admin panel
(optional separate package: antonioprimera/laravel-admin-panel) and use them in your blade files, to
display language specific texts and media - yes, you can even use different images for different languages.

## Architecture (or how it all works)

The web page uses WebComponents and WebBits to be able to store any configurable data. WebComponents
are used to group other WebComponents and WebBits. WebBits are used to store texts, image urls or
any other useful data.

WebComponents and WebBits are Eloquent Models and will be called in general WebItems. These can be
extended, to implement more specific WebItems.

### WebPage

The WebPage object is the root object for any WebItem and is used to create and retrieve them.

The WebPage object also sets and holds the current language and the available languages for a project.

You can access the WebPage through the Facade `AntonioPrimera\WebPage\Facades\WebPage` or through
the helper function `webPage()`.

### WebComponents

WebComponents are Eloquent Models, which can be organized in a hierarchy, having as parent either the
WebPage itself (which is the root of this hierarchy), or another WebComponent.

WebComponents can have as children other WebComponents and / or WebBits.

### WebBits

WebBits are models, considered data nodes, contrary to WebComponents, which are providing the WebPage
structure. WebComponents are branches and WebBits are leaves.

WebBits can have relations to other models, like Media Items from Spatie Laravel Media Library.

The main role of WebBits is to store data in one or many languages. These are specific to multi-lingual
websites.

WebBit data is stored as an array, so it can hold some data several levels deep.

### Addressing and retrieving WebItems

WebItems are addressed by their unique ids, available as their `uid` attribute.

WebComponents can be addressed by their dot-separated uid path. E.g.

```php
$heroComponent = WebPage::getComponent('home-page.header.hero');
//or
$heroComponent = WebPage::get('home-page.header.hero');
```

WebBits can be retrieved by their uid, from the parent WebComponent or by addressing them using the
parent WebComponent address, adding the WebBit uid as suffix, separated by a semicolon. E.g.

```php
$heroComponent = WebPage::getComponent('home-page.header.hero');
$pageTitle = $heroComponent->getBit('title');
//or
$pageTitle = WebPage::get('home-page.header.hero:title');
```

### Getting and setting WebBit data

The WebBit has a `data` attribute, which holds the bit data as an array. The first array level is the
language key (e.g. 'en') and the next level is usually the actual data corresponding to that language. E.g.

```php
//setting bit data
$bit->set('en', 'Awesome Page Title');
$bit->set('de', 'Awesome Page Title, but in German');
//or directly
WebPage::get('home-page.header.hero:title')->set('es', 'El titlo Espanol - this might not be Spanish ;)');

//retrieving bit data
$bit->get('en');
```

Usually, you can ignore the language attribute when retrieving the bit data, to retrieve the data
corresponding to the current WebPage language.

Optionally, you can save deep nested language specific data, by using a dot separated path. E.g.

```php
//setting nested bit data
$pageTitle->set('en', 'Awesome Page Title', 'text.visible');
$pageTitle->set('en', 'This is the page title: Awesome Page Title', 'text.screen-reader');
//or
$pageTitle->set('en', ['text' => ['visible' => '...', 'screen-reader' => '...']]);

//retrieving bit data
$pageTitle->get('en', 'text.visible');
```

## Creating WebItems

The idea behind having WebComponents and WebBits in your application is to have them created once,
during the setup of the site and then reuse them and maintain their contents (media, bit text etc.).

Creating WebComponents and WebBits can be easily done using Recipes - via artisan commands.

### Creating a recipe

You can create a recipe using the following artisan command:

`php artisan web-page:recipe:create RecipeName [--complex]`

This will create a new recipe class in a folder of your project: app/WebPage/Recipes. If this folder
doesn't exist, it will be created.

A simple recipe class will extend the `AntonioPrimera\WebPage\Recipes\Recipe` class and will
contain a public `recipe()` method, returning an array of items to be created.

If you need more control, you can use the `--complex` flag when creating the recipe, which will
add the `up()` and `down()` methods to your recipe, which you can override.

#### How to write a recipe

WebComponents are grouped in an item keyed **components** and WebBits in an item named **bits**. The first
level of WebItems can only have components (the WebPage can only have Components as children, not bits),
so you can skip wrapping them in a `components` item. 

Here's a recipe example, which you could use in a recipe file:

```php
public function recipe(): array
{
    return [
        'Page:HomePage:home-page' => [
        
            'components' => [
                'Section:Header:header' => [
                    'components' => [...],
                    'bits' => [...],
                ],
                'Section:Gallery:main-gallery' => [
                    'model' => AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent::class,
                ],
            ],
            
            'bits' => [
                'Title:PageTitle:page-title',
                'Image:Hero:hero-image' => [
                    'model' => App\Models\WebPage\HeroImage::class,
                ],
            ],
        ],
        
        //other components
    ];
}
```

Each WebItem (component or bit) has a signature and optionally a definition. If it requires a definition
(using a specific model or if it has some child components / bits), the signature will be the key. If
it's a simple component and bit, or if its type definition is available in the `webComponents.php` or
`webBits.php` config files, you can have the signature as the item (like the first bit in the above example).

#### WebItem signature

The signature of a WebItem (WebComponent / WebBit) is a semicolon separated list of 1, 2 or 3 items. A
full signature, of 3 items is as follows: **Type:Name:Uid**.

If you omit the uid, the WebItem uid will be inferred from its name, and will be the kebab representation
of the name. For example, a WebItem with the signature **Page:HomePage** will have the type "Page", the
name "HomePage" and the uid "home-page".

You can also omit the name, in which case the WebItem name will be the same as its type. For example,
a WebItem with the signature **Gallery** will have the type "Gallery", the name "Gallery" and the uid
"gallery".

The WebItem uid, should be unique among its siblings, in order to be able to address and retrieve a
specific item by its uid. You can reuse uids for items which aren't siblings.

#### WebItem model

Although WebItems are basically Eloquent Models, you can extend the base models and have component instances
of classes extending the base WebComponent Eloquent Model and bit instances of classes extending the base
WebBit Eloquent Model.

Under the hood, each WebItem has a 'class_name' attribute and when retrieving WebComponents and WebBits
using the package's methods (WebPage::get(), $webComponent->getComponents() etc.), the models are
deserialized into the corresponding classes. These should always be classes extending the base WebComponent,
respectively the base WebBit class.

You can define the class of a WebItem, by setting its `model` attribute in the recipe (see above example).

This allows you to extend the base `AntonioPrimera\WebPage\Models\WebComponent` and the
`AntonioPrimera\WebPage\Models\WebBit` class and add specific functionality to your WebItems. This is how
the Image Gallery WebComponent `AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent` was created,
which allows you to attach a set of images to id and maintain them inside the AdminPanel (if implemented
in your project).

Another such special WebItem is the Image WebBit model `AntonioPrimera\WebPage\Models\Bits\ImageBit`,
which allows you to attach language specific images (one image for each language). This allows you to
show an image when the page is displayed in English and another image when the page is displayed in another
specific language.

### Running a recipe

You can run an existing recipe file by using the following artisan command:

`php artisan web-page:recipe:run RecipeName`

This will run the `up()` method of the recipe, by default, creating the defined components and bits.
To revert the recipe

If you want to trash / revert a recipe, you can add the `--down` flag to the above command. This will
run the `down()` method of the recipe.

## Integration with the Admin Panel

This package integrates seamlessly with the Admin Panel package
[`antonioprimera/laravel-admin-panel`](https://packagist.org/packages/antonioprimera/laravel-admin-panel).
When the Admin Panel package is available in the project, the WebPage package registers an admin page
for each WebComponent model, which is a direct child of the WebPage.

#### How this all works

Each WebItem exposes a public methods `getAdminViewComponent()` returning the Livewire Component class
for the AdminPage. Additionally, a `getAdminViewData()` method can be overridden, returning an array
of attributes to be passed to the Livewire Admin View Component.

So, if you want to roll your own WebComponents and WebBits, you can create specific Livewire admin
components. The admin components of the child WebItems should be nested on the parent admin component page.

## Configuration

The WebPage package comes with 3 config files:

- config/webPage.php 
- config/webComponents.php
- config/webBits.php

**The webPage config** will hold generic configuration, which might apply to different WebItems, like
media disks (where the uploaded Media is stored), media conversions (how Media files are manipulated once
uploaded) etc.

**The webComponents config** will only define WebComponent types and their structure (recipe), so that,
when you want to create a new WebComponent with a configured type, its configured recipe will be used.
For example, this is the specification for WebComponents of type "Gallery":

```php
return [
    //... other web component types
    
	'Gallery' => [
		'model' => '\\AntonioPrimera\\WebPage\\Models\\Components\\ImageGalleryComponent',
		'bits' => [
		    'Title', 'Description'
        ],
	],
];
```

This allows you to just add a component of type 'Gallery' in your recipe (see WebItem signatures), which
will have the configured class and will have a child WebBit of type 'Title' and one of type 'Description'.

**The webBits config** has the same scope as the webComponents config, but it applies to WebBit creation.
The only thing you can configure for WebBits is its model class, because WebBits don't have any child
components.

### Maintaining the media disks

By default, the GalleryWebComponent admin tries to save the media files to the 'public' disk. You
can change the disk for all components and bits, or you can set an individual disk for each component
or bit.

To change the disk for all bit and gallery media, configure the disk in the webPage config, under the
key 'disks.default'.

```php
//file: config/webPage.php
return [
    'disks' => [
        'default' => 'gallery-media-disk',
    ],
];
```


To set the disk for a specific gallery or a specific bit, configure the disk name in the webPage config
file, under the key 'disks.<component-or-bit-uid>'.

```php
//file: config/webPage.php
return [
    'disks' => [
        'my-media-gallery' => 'gallery-media-disk',
    ],
];
```

If you have several components or bits with the same uid, and you want to define different media
upload disks for each one, you can configure the disk name replicating the bit / component path. For
example, to configure different disks for 2 components named 'media-gallery', you can do something
like this:

```php
//file: config/webPage.php
return [
    'disks' => [
        'tours' => [
            'media-gallery' => 'tours-media-disk',
        ],
        'about-me' => [
            'media-gallery' => 'my-media-disk',
        ],
    ],
];
```

To change the disk for a specific WebComponent / WebBit class, you can just add a configuration set
for that class in the webPage config. The default WebComponent gallery model is
`\AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent`. The default WebBit component is
`\AntonioPrimera\WebPage\Models\WebBit`.

```php
//file: config/webPage.php
return [
    'AntonioPrimera\\WebPage\\Models\\Components\\ImageGalleryComponent' => [
        'disk' => 'gallery-media-disk',
        //...
    ],
];
```

### Configuring Media Conversions

If you want to create media conversions for your uploaded media, you can configure a set of media
conversions for each specific model class which handles media files (using
[Spatie's Media Gallery package](https://spatie.be/docs/laravel-medialibrary/v9/introduction)).

For more information about the media conversions (values, options, restrictions, server requirements
etc.), check out [Spatie's Image package](https://spatie.be/docs/image/v1/introduction).

Below you can find the available manipulations / conversions. You can mix any of the conversions and
image manipulations below inside a media conversion. You can define any number of media conversions,
but please understand that media conversions require a lot of time and processing power.

After media conversions are applied, you can request the url for a converted image like follow.

```html
//for a media item with a conversion named 'square-500'
<img src="{{ $media->getUrl('square-500') }}" alt="{{ $media->getCustomProperty('alt') }}">
```

#### Image Format

You can convert your image to any of the formats defined by `\Spatie\Image\Manipulations`. For example
to convert your image to webp, you would do something like this:

```php
<?php
//file: config/webPage.php
return [
    \AntonioPrimera\WebPage\Models\Components\ImageGalleryComponent::class => [
        'mediaConversions' => [
            'webp-500' => [
                'format' => \Spatie\Image\Manipulations::FORMAT_WEBP,
                'width' => 500,
                //... other manipulations
            ],
            
            //... other media conversions
        ],
    ],
]; 
```

#### Resize: Width and Height

You can constrain the width and height of the converted image to a set of maximum width and maximum
height values (in pixels). You can specify the width or the height, or both.

```php
<?php
//file: config/webPage.php
return [
    \App\Models\WebComponents\Gallery::class => [
        'mediaConversions' => [
            'max-500' => [
                'width' => 500,
                'height' => 500,
            ],
        ],
    ],
]; 
```

#### Resize: Fit

You can resize an image using the "Fit" manipulation, as described
[here](https://spatie.be/docs/image/v1/image-manipulations/resizing-images#content-fit). For this,
you can create a conversion, with a 'fit' key, holding an array with 'width' (mandatory), 'height'
(mandatory) and 'method' (optional, default: Manipulations::FIT_CONTAIN).

For example, to resize an image to a square, similar with the CSS `object-fit: cover`, you would
do something like this:

```php
<?php
//file: config/webPage.php
return [
    \App\Models\WebComponents\Gallery::class => [
        'mediaConversions' => [
            'square-500' => [
                'fit' => [
                    'width'  => 500,
                    'height' => 500,
                    'method' => \Spatie\Image\Manipulations::FIT_CROP,
                ],
            ],
        ],
    ],
]; 
```

#### Jpeg quality

You can convert JPEGs to a specified quality (int between 1 and 100), by setting the 'quality'
attribute.

```php
<?php
//file: config/webPage.php
return [
    \App\Models\WebComponents\Gallery::class => [
        'mediaConversions' => [
            'low-quality' => [
                'quality' => 20,    //this will reduce the image quality and size by a lot
            ],
        ],
    ],
]; 
```

#### Artistic manipulations

You can choose any of the following manipulations, by adding them to your media conversion (with
an example for each).

- Greyscale (boolean) e.g. `'greyscale' => true`
- Sepia (boolean) e.g. `'sepia' => true`
- Sharpen (int) e.g. `'sharpen' => 80`
- Blur (int) e.g. `'blur' => 50`
- Pixelate (int - pixel size) e.g. `'pixelate' => 20`
- Brightness (int - positive or negative) e.g. `'brightness' => -40`

For example, to create a sharpened, greyscale and slightly darker image, you could do something like
this:

```php
<?php
//file: config/webPage.php
return [
    \App\Models\WebComponents\Gallery::class => [
        'mediaConversions' => [
            'artsy' => [
                'sharpen'    => 50,
                'greyscale'  => true,
                'brightness' => -20,
                'width'      => 1920,
            ],
        ],
    ],
]; 
```

### Media Custom Properties

The custom properties are a set of metadata assigned to each media item. For example, you might want to
let the user maintain an 'alt' attribute or a label for each uploaded image.

You can define the list of custom properties for each media set, as an array of custom property names,
(for example `['alt', 'label']`), in the following places, sorted by priority - first ones are those with
higher priority:

#### Config: webPage.mediaProperties.{itemPath}

For example, if you have a Gallery component addressable with `'home-page.gallery'`, you would maintain
the list of custom properties, so that it's accessible via
`config('webPage.mediaProperties.home-page.gallery')`.

This is the most specific configuration and allows you to define different custom properties sets for each
individual WebItem handling media items.

#### Config: webPage.mediaProperties.{uid}

For example, if you have a Gallery component with uid `'gallery'`, you would maintain the list of custom
properties, so that it's accessible via `config('webPage.mediaProperties.gallery')`.

This would address all WebItems with uid '`gallery`'. This approach might be sufficient for most projects.

#### Config: webPage.{className}.mediaProperties

This is a good approach if you already have another class specific configuration (like media conversions).
You would then just add the `'mediaProperties'` attribute and define your custom properties there. This
configuration would apply to all WebItems with this class.

#### Config: webPage.mediaProperties.default

If you want to define a default set of media properties for all your media items, you can configure it,
so that it's accessible via `config('webPage.mediaProperties.default')`.

Of course, you can have a default set and use other, more specific config methods to override this default,
for specific WebItems.

#### Instance attribute: $mediaProperties

Each WebItem class handling media has a $mediaProperties attribute, defining a sensible default list of
custom media properties. At the moment, this default is `['label']`, which can be used as a media label
and also as the value of the `alt` html attribute.