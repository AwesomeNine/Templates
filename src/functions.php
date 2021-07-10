<?php
/**
 * Template functions
 *
 * @since   1.0.0
 * @package Awesome9\Templates
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Templates;

/**
 * Prints the template
 *
 * @since  1.0.0
 *
 * @param  string $storage Storage name.
 * @param  string $name    Template name.
 * @param  array  $vars    Tempalte variables. Default: empty.
 */
function template( $storage, $name, $vars = [] ) {
	( new Template( $storage, $name, $vars ) )->render();
}

/**
 * Outputs the template
 *
 * @since  1.0.0
 *
 * @param  string $storage Storage name.
 * @param  string $name    Template name.
 * @param  array  $vars    Tempalte variables. Default: empty.
 * @return string
 */
function get_template( $storage, $name, $vars = [] ) {
	return ( new Template( $storage, $name, $vars ) )->output();
}
