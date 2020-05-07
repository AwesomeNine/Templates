<?php
/**
 * Class TestTemplate
 *
 * @package Awesome9\Templates
 */

namespace Awesome9\Templates\Test;

use Awesome9\Templates\Storage;
use Awesome9\Templates\Template;
use Awesome9\Templates\Exceptions;

/**
 * Template test case.
 */
class TestTemplate extends \WP_UnitTestCase {

	/**
	 * @expectedException Awesome9\Templates\Exceptions\TemplateException
	 */
	public function test_should_throw_if_vars_not_array_exception() {
		new Template( 'test', 'template', 'var' );
	}

	public function test_should_get_name() {
		$name     = '/templates/section/template';
		$template = new Template( 'test', $name );
		$this->assertSame( $name, $template->get_name() );
	}

	public function test_should_return_path() {
		$name     = '/templates/section/template';
		$template = new Template( 'test', $name );
		$this->assertSame( sprintf( '%s%s.php', wp_normalize_path( WP_PLUGIN_DIR ) . '/test/tmp/test', $name ), $template->get_path() );
	}

	public function test_should_return_variable() {
		$vars = [
			'first'  => uniqid(),
			'second' => 'test',
		];

		$template = new Template( 'test', 'test', $vars );

		$this->assertSame( $vars['first'], $template->get( 'first' ) );
		$this->assertSame( $vars['second'], $template->get( 'second' ) );

	}

	public function test_should_return_null_if_var_not_set() {
		$vars = [
			'first'  => uniqid(),
			'second' => 'test',
		];

		$template = new Template( 'test', 'test', $vars );

		$this->assertSame( null, $template->get( 'test' ) );


	}

	public function test_should_print_var() {

		$vars = [
			'first' => uniqid(),
		];

		$template = new Template( 'test', 'test', $vars );

		$this->expectOutputString( $vars['first'] );
		$template->the( 'first' );

	}

	public function test_should_print_nothing_if_var_not_set() {

		$vars = [
			'first' => uniqid(),
		];

		$template = new Template( 'test', 'test', $vars );

		$this->expectOutputString( '' );
		$template->the( 'test' );

	}

	public function test_should_set_var_and_return_object() {

		$value    = uniqid();
		$template = new Template( 'test', 'test' );

		$return = $template->set( 'var', $value );

		$this->assertSame( $value, $template->get( 'var' ) );
		$this->assertSame( $template, $return );

	}

	public function test_should_set_vars_and_return_object() {

		$vars = [
			'first'  => uniqid(),
			'second' => 'test',
		];

		$template = new Template( 'test', 'test' );

		$return = $template->set( 'first', $vars['first'] )
						   ->set( 'second', $vars['second'] );

		$this->assertSame( $vars['first'], $template->get( 'first' ) );
		$this->assertSame( $vars['second'], $template->get( 'second' ) );
		$this->assertSame( $template, $return );

	}

	public function test_should_return_variables() {

		$vars = [
			'first'  => uniqid(),
			'second' => 'test',
		];

		$template = new Template( 'test', 'test', $vars );

		$this->assertSame( $vars, $template->get_vars() );

	}

	public function test_should_remove_var() {

		$vars = [
			'test' => uniqid(),
		];

		$template = new Template( 'test', 'test', $vars );

		$template->remove( 'test' );

		$this->assertNull( $template->get( 'test' ) );

	}

	public function test_should_clear_all_vars() {

		$vars = [
			'first'  => uniqid(),
			'second' => 'test',
		];

		$template = new Template( 'test', 'test', $vars );

		$template->clear_vars();

		$this->assertSame( [], $template->get_vars() );

	}

	/**
	 * @expectedException Awesome9\Templates\Exceptions\TemplateException
	 */
	public function test_should_throw_exception_if_template_not_found() {
		$template = new Template( 'test', 'not-existing' );
		$template->render();
	}

	/**
	 * @expectedException Awesome9\Templates\Exceptions\StorageException
	 */
	public function test_should_throw_exception_if_storage_not_found() {
		$template = new Template( 'no-storage', '/assets/template-no-var' );
		$template->render();
	}
}
