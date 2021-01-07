# Template

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d20643c660874a8cb7b1398fca1d93bf)](https://app.codacy.com/gh/AwesomeNine/Templates?utm_source=github.com&utm_medium=referral&utm_content=AwesomeNine/Templates&utm_campaign=Badge_Grade)
[![Awesome9](https://img.shields.io/badge/Awesome-9-brightgreen)](https://awesome9.co)
[![Latest Stable Version](https://poser.pugx.org/awesome9/templates/v/stable)](https://packagist.org/packages/awesome9/templates)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/awesome9/templates.svg)](https://packagist.org/packages/awesome9/templates)
[![Total Downloads](https://poser.pugx.org/awesome9/templates/downloads)](https://packagist.org/packages/awesome9/templates)
[![License](https://poser.pugx.org/awesome9/templates/license)](https://packagist.org/packages/awesome9/templates)

<p align="center">
	<img src="https://img.icons8.com/nolan/256/stocks.png" />
</p>

## 📃 About Template

This package provides ease of loading templates intended to be used within a plugin or theme and also provide a template system like WooCommerce.

The inspiration for the package comes from [Templates micropackage](https://github.com/micropackage/templates).

## 💾 Installation

``` bash
composer require awesome9/templates
```

## 🕹 Usage

First, you need to initialize your storage.

```php
Awesome9\Templates\Storage::get()
	->set_basedir( dirname( __FILE__ ) )
	->set_baseurl( plugins_url( __FILE__ ) );
```

#### Case # 1: Random folders
Let's assume your template tree looks like this:

```
my-plugin/
├── admin/
│   └── templates/
│      ├── notice.php
│      └── settings.php
└── frontend/
	└── templates/
	   ├── profile.php
	   └── welcome.php
```

In the above case we have two places with templates, let's define them as storages.

```php
Awesome9\Templates\Storage::get()->add( 'admin', 'admin/templates' );
Awesome9\Templates\Storage::get()->add( 'frontend', 'frontend/templates' );
```

Then you can easily render template:

```php
$template = new Awesome9\Templates\Template( 'frontend', 'author', [
	'author' => $user_name,
	'posts'  => get_posts( [ 'author' => $user_id ] ),
] );

$template->render();
```

The template file could look like this:

```php
<p>Howdy, <?php $this->the( 'author' ); ?></p>

<p>Posts by Author:</p>

<ul>
	<?php foreach ( $this->get( 'posts' ) as $post ) : ?>
		<li><?php echo $post->post_title; ?></li>
	<?php endforeach; ?>
</ul>
```

----------------------

#### Case # 2: Template from theme first
Let's assume your template tree looks like this:

```
my-plugin/
├── templates/
│   ├── notice.php
│   └── profile.php

some-theme/
├── my-plugin/
│   ├── notice.php
│   └── settings.php
```

In this case, we set both plugin and theme folder name for template lookup.

```php
Awesome9\Templates\Storage::get()->set_for_theme( 'templates', 'my-plugin' );
```

### Accessing variables in the template file

In the template file, `$this` points to the template instance, which means you can access all the template methods.

The basic usage is:

```php
$this->the( 'var_name' ); // Prints the value.
$var_name = $this->get( 'var_name' ); // Gets the value.
```

But you can also use the shorthand closure methods:

```php
$the( 'var_name' ); // Prints the value.
$var_name = $get( 'var_name' ); // Gets the value.
```

### Default variable values

When variable is not defined, you can specify its default value:

```php
$the( 'var_name', 'Default val' );
$var_name = $get( 'var_name', 'Default val' );
```

### Available template methods

Template class methods.

| Method                                          | Description                       | Returns                                                      |
| ----------------------------------------------- | --------------------------------- | ------------------------------------------------------------ |
| ```get_path()```                                | Gets full path with extension     | *(string)*                                                   |
| ```get_vars()```                                | Gets all variables                | *(array)*                                                    |
| ```clear_vars()```                              | Clears all variables              | `$this`                                                      |
| ```set((string) $var_name, (string) $value )``` | Sets the variable value           | `$this`                                                      |
| ```get( (string) $var_name )```                 | Gets the variable value           | *(mixed\|null)*<br />Null if variable with given name wasn't set |
| ```the( (string) $var_name )```                 | Prints the variable value         | void                                                         |
| ```remove( (string) $var_name )```              | Removes the variable              | `$this`                                                      |
| ```render()```                                  | Renders the template              | void                                                         |
| ```output()```                                  | Outputs the template              | *(string)*                                                   |

### Template constructor params

```php
$template = new Awesome9\Templates\Template(
	$storage_name = 'frontend',
	$template_name = 'profile',
	$variables  = [
		'var_key' => $var_value,
	]
);
```

| Parameter            | Type         | Description                                                  |
| -------------------- | ------------ | ------------------------------------------------------------ |
| ```$storage_name```  | **Required** | Must match registered storage                                |
| ```$template_name``` | **Required** | Relative template path, example:<br />`user/section/profile` will be resolved to:<br />`$storage_path . '/user/section/profile.php'` |
| ```$variables```     | Optional     | Array of template variables in format:<br />`key => value`<br />Can be added later with `set()` method |

### Helper functions

You can use the procedural approach as well:

```php
// Print the template.
Awesome9\Templates\template( $storage_name, $template_name, $variables );

// Get the template output.
Awesome9\Templates\get_template( $storage_name, $template_name, $variables );
```

All the parameters remains the same as for the `Template` class.


## 📖 Changelog

[See the changelog file](./CHANGELOG.md)
