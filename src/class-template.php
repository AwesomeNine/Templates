<?php
/**
 * Template class
 *
 * @since   1.0.0
 * @package Awesome9\Templates
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Templates;

use Awesome9\Templates\Exceptions\Storage_Exception;
use Awesome9\Templates\Exceptions\Template_Exception;

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
	private $vars = [];

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
	 * @throws Storage_Exception  When storage wasn't found.
	 * @throws Template_Exception When variables is not an array.
	 *
	 * @param  string $storage Storage name.
	 * @param  string $name    Template name.
	 * @param  array  $vars    Tempalte variables. Default: empty.
	 * @return Template
	 */
	public function __construct( $storage, $name, $vars = [] ) {
		$this->storage = $storage;
		$this->name    = $name;

		if ( ! Storage::get()->is_exists( $storage ) ) {
			throw new Storage_Exception( sprintf( 'Storage %s wasn\'t found', $storage ) );
		}

		if ( ! is_array( $vars ) ) {
			throw new Template_Exception( sprintf( 'Template %s vars should be an array', $name ) );
		}

		$this->vars   = $vars;
		$this->locate = 'templates' === $storage;

		return $this;
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
		} catch ( Template_Exception $e ) {
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
				[
					Storage::get()->get_path( 'theme' ) . "{$this->name}.php",
					Storage::get()->get_path( 'templates' ) . "{$this->name}.php",
				]
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
	 * Clears all template variables
	 *
	 * @since  1.0.0
	 *
	 * @return Template
	 */
	public function clear_vars() {
		$this->vars = [];
		return $this;
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
	 * Renders the template
	 *
	 * @since  1.0.0
	 *
	 * @throws Template_Exception If template file does not exist.
	 */
	public function render() {
		if ( ! $this->is_exists() ) {
			throw new Template_Exception( sprintf( 'Template file "%s" does not exist', $this->get_path() ) );
		}

		$get_method = [ $this, 'get' ];
		$get        = function () use ( $get_method ) {
			return call_user_func_array( $get_method, func_get_args() );
		};

		$the_method = [ $this, 'the' ];
		$the        = function () use ( $the_method ) {
			return call_user_func_array( $the_method, func_get_args() );
		};

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
