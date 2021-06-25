<?php
/**
 * Add expire event to Workflows with dynamic post author recipient.
 */

namespace HM\Content_Expiration\Workflows;

use const HM\Content_Expiration\SUPPORTS_KEY;
use HM\Workflows;
use HM\Workflows\Event;

/**
 * Register actions and filters.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'hm.workflows.init', __NAMESPACE__ . '\\setup_workflows', 1 );
}

/**
 * Setup Workflows event so that site administrators can configure custom workflows.
 *
 * @return void
 */
function setup_workflows() {

	$post_types = get_post_types_by_support( SUPPORTS_KEY );

	// Bail if no post type supports expiration.
	if ( empty( $post_types ) ) {
		return;
	}

	$event = Event::register( 'expired' );

	$event->set_listener( [
		'action' => 'transition_post_status',
		'callback' => function ( $new_status, $old_status, $post ) {
			if ( $new_status === $old_status ) {
				return null;
			}

			if ( $new_status !== 'expired' ) {
				return null;
			}

			return $post;
		},
		'accepted_args' => 3,
	] );

	$event->add_message_tags( Workflows\get_messages_tags() );

	$event->add_message_action(
		'view',
		__( 'View post', 'content-expiration' ),
		function ( $post_id ) {
			return get_the_permalink( $post_id );
		},
		function ( $post ) {
			return [ 'post_id' => get_post( $post )->ID ];
		},
		[ 'post_id' => 'intval' ]
	);

	$event->add_message_action(
		'edit',
		__( 'Edit post', 'content-expiration' ),
		function ( $post_id ) {
			return get_edit_post_link( $post_id, 'raw' );
		},
		function ( $post ) {
			return [ 'post_id' => get_post( $post )->ID ];
		},
		[ 'post_id' => 'intval' ]
	);

	$event->add_recipient_handler(
		'author',
		function ( $post ) {
			return [ get_user_by( 'id', $post->post_author ) ];
		},
		__( 'Post Author', 'content-expiration' )
	);

	$event->add_ui( __( 'A post expires', 'content-expiration' ) );

	$event->get_ui()->add_field(
		'type',
		__( 'Post types', 'content-expiration' ),
		'select',
		[
			'options' => array_values(
				array_map(
					function ( $post_type ) {
						$post_type_object = get_post_type_object( $post_type );
						return [
							'label' => $post_type_object->label,
							'value' => $post_type_object->name,
						];
					},
					$post_types
				)
			),
		]
	);
}
