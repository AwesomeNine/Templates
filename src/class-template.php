<?php
/**
 * Template class
 *
 * @since   1.0.0
 * @package Awesome9\Templates
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Templates;

use Awesome9\Templates\Exceptions\StorageException;
use Awesome9\Templates\Exceptions\TemplateException;

/**
 * Template class
 */
class Template {

	/**
	 * Storage name.
	 *
	 * @var string
	 */
	private $storage;

	/**
	 * Template name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Template variables
	 *
	 * @var array
	 */
	private $vars = array();

	/**
	 * Start locating from theme folder.
	 *
	 * @var bool
	 */
	private $locate = false;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 *
	 * @throws StorageException  When storage wasn't found.
	 * @throws TemplateException When variables is not an array.
	 *
	 * @param  string $storage Storage name.
	 * @param  string $name    Template name.
	 * @param  array  $vars    Tempalte variables. Default: empty.
	 * @return Template
	 */
	public function __construct( $storage, $name, $vars = array() ) {
		$this->storage = $storage;
		$this->name    = $name;

		if ( ! Storage::get()->is_exists( $storage ) ) {
			throw new StorageException( sprintf( 'Storage %s wasn\'t found', $storage ) );
		}

		if ( ! is_array( $vars ) ) {
			throw new TemplateException( sprintf( 'Template %s vars should be an array', $name ) );
		}

		$this->vars   = $vars;
		$this->locate = 'templates' === $storage;
	}

	/**
	 * Magic method for string conversion
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->output();
		} catch ( TemplateException $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * Gets template name
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets path with extension
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_path() {
		if ( $this->locate ) {
			return locate_template(
				array(
					Storage::get()->get_path( 'theme' ) . "{$this->name}.php",
					Storage::get()->get_path( 'templates' ) . "{$this->name}.php",
				)
			);
		}

		return Storage::get()->get_path( $this->storage ) . "{$this->name}.php";
	}

	/**
	 * Checks if template file exists
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_exists() {
		return file_exists( $this->get_path() );
	}

	/**
	 * Gets all template variables
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function get_vars() {
		return $this->vars;
	}

	/**
	 * Prints the template var
	 *
	 * @since  1.0.0
	 *
	 * @param  string $var_name Template var name.
	 * @param  mixed  $default  Template var default value.
	 */
	public function the( $var_name, $default = null ) {
		echo (string) $this->get( $var_name, $default ); // phpcs:ignore
	}

	/**
	 * Gets template var value
	 *
	 * @since  1.0.0
	 *
	 * @param  string $var_name Template var name.
	 * @param  mixed  $default  Template var default value.
	 * @return mixed|null       Null if var not set.
	 */
	public function get( $var_name, $default = null ) {
		if ( isset( $this->vars[ $var_name ] ) ) {
			return $this->vars[ $var_name ];
		}

		return $default;
	}

	/**
	 * Sets template var value
	 *
	 * @since  1.0.0
	 *
	 * @param  string $var_name Template var name.
	 * @param  mixed  $value    Var value.
	 * @return Template
	 */
	public function set( $var_name, $value ) {
		$this->vars[ $var_name ] = $value;
		return $this;
	}

	/**
	 * Removes the template var
	 *
	 * @since  1.0.0
	 *
	 * @param  string $var_name Template var name.
	 * @return Template
	 */
	public function remove( $var_name ) {
		unset( $this->vars[ $var_name ] );
		return $this;
	}

	/**
	 * Clears all template variables
	 *
	 * @since  1.0.0
	 *
	 * @return Template
	 */
	public function clear_vars() {
		$this->vars = array();
		return $this;
	}

	/**
	 * Renders the template
	 *
	 * @since  1.0.0
	 *
	 * @throws TemplateException If teplate file does not exist.
	 */
	public function render() {
		if ( ! $this->is_exists() ) {
			throw new TemplateException( sprintf( 'Template file "%s" does not exist', $this->get_path() ) );
		}

		$get = \Closure::fromCallable( array( $this, 'get' ) );
		$the = \Closure::fromCallable( array( $this, 'the' ) );

		include $this->get_path();
	}

	/**
	 * Outputs the template
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function output() {
		ob_start();
		$this->render();
		return ob_get_clean();
	}
}
