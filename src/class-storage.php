<?php
/**
 * Storage class
 *
 * @since   1.0.0
 * @package Awesome9\Templates
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Templates;

use Awesome9\Templates\Exceptions\StorageException;

/**
 * Storage class
 */
class Storage {

	/**
	 * Base directory with trailing slash
	 *
	 * @var string
	 */
	private $base_dir;

	/**
	 * Base url with trailing slash
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * Storages
	 *
	 * @var array
	 */
	private $storages = array();

	/**
	 * Retrieve main Storage instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return Storage
	 */
	public static function get() {
		static $instance;

		if ( is_null( $instance ) && ! ( $instance instanceof Storage ) ) {
			$instance = new Storage();
		}

		return $instance;
	}

	/**
	 * Set base directory.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $base_dir Absolute path to the base dir.
	 * @return Storage
	 */
	public function set_basedir( $base_dir ) {
		$this->base_dir = trailingslashit( wp_normalize_path( $base_dir ) );

		return $this;
	}

	/**
	 * Set base url.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $base_url Absolute path to the base dir.
	 * @return Storage
	 */
	public function set_baseurl( $base_url ) {
		$this->base_url = trailingslashit( $base_url );

		return $this;
	}

	/**
	 * Set plugin template folder with theme folder priority.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $plugin_folder Plugin folder for templates.
	 * @param  string $theme_folder  Theme folder for templates.
	 * @return Storage
	 */
	public function set_for_theme( $plugin_folder, $theme_folder ) {
		$this->storages['templates'] = array(
			'path' => $this->base_dir . $plugin_folder,
			'url'  => $this->base_url . $plugin_folder,
		);

		$this->storages['theme'] = array(
			'path' => trailingslashit( get_template_directory() ) . $theme_folder,
			'url'  => trailingslashit( get_template_directory_uri() ) . $theme_folder,
		);

		return $this;
	}

	/**
	 * Adds new storage
	 *
	 * @since  1.0.0
	 *
	 * @throws StorageException When storage with given name already exists.
	 * @throws StorageException When storage base path doesn't exist or is not a dir.
	 *
	 * @param  string $name   Storage reference name.
	 * @param  string $folder Storage base absolute path.
	 * @return Storage
	 */
	public function add( $name, $folder ) {
		if ( isset( $this->storages[ $name ] ) ) {
			throw new StorageException( sprintf( 'Storage %s already exists', $name ) );
		}

		$this->storages[ $name ] = array(
			'path' => $this->base_dir . $folder,
			'url'  => $this->base_url . $folder,
		);

		return $this;
	}

	/**
	 * Is storage exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name Storage reference name.
	 * @return bool
	 */
	public function is_exists( $name ) {
		return isset( $this->storages[ $name ] );
	}

	/**
	 * Gets storage path
	 *
	 * @since  1.0.0
	 *
	 * @throws StorageException When storage is not found.
	 *
	 * @param  string $name Storage reference name.
	 * @return string
	 */
	public function get_path( $name ) {
		if ( ! isset( $this->storages[ $name ] ) ) {
			throw new StorageException( sprintf( 'Storage %s does not exist', $name ) );
		}

		return $this->storages[ $name ]['path'];
	}

	/**
	 * Gets storage url
	 *
	 * @since  1.0.0
	 *
	 * @throws StorageException When storage is not found.
	 *
	 * @param  string $name Storage reference name.
	 * @return string
	 */
	public function get_url( $name ) {
		if ( ! isset( $this->storages[ $name ] ) ) {
			throw new StorageException( sprintf( 'Storage %s does not exist', $name ) );
		}

		return $this->storages[ $name ]['url'];
	}
}
