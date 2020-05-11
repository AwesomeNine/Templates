<?php
/**
 * Class TestStorage
 *
 * @since   1.0.0
 * @package Awesome9\Templates
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Templates\Test;

use Awesome9\Templates\Storage;
use Awesome9\Templates\Exceptions\StorageException;

/**
 * Storage test case.
 */
class TestStorage extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		Storage::get()
			->set_basedir( WP_PLUGIN_DIR . '/test/' )
			->set_baseurl( WP_PLUGIN_URL . '/test/' )
			->set_for_theme( 'layouts', 'my-plugin' );
	}

	/**
	 * @expectedException Awesome9\Templates\Exceptions\StorageException
	 */
	public function test_should_throw_storage_already_exist_exception() {
		Storage::get()
			->add( 'test', 'tmp/test' )
			->add( 'test', 'tmp/test' );
	}

	/**
	 * @expectedException Awesome9\Templates\Exceptions\StorageException
	 */
	public function test_should_throw_storage_not_exists_exception() {
		Storage::get()->get_path( 'doesntexist' );
	}

	public function test_add_and_get_storage() {
		$this->assertSame( wp_normalize_path( WP_PLUGIN_DIR ) . '/test/tmp/test', Storage::get()->get_path( 'test' ) );
		$this->assertSame( WP_PLUGIN_URL . '/test/tmp/test', Storage::get()->get_url( 'test' ) );
	}

	public function test_theme_path_with_fallback() {
		$this->assertSame( wp_normalize_path( WP_PLUGIN_DIR ) . '/test/layouts', Storage::get()->get_path( 'templates' ) );
		$this->assertSame( get_template_directory() . '/my-plugin', Storage::get()->get_path( 'theme' ) );
	}
}
