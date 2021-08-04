# Content Expiration

Content expiration plugin for the HM Playbook. Allows content of any post type to expire after set period, based on post modified time (defaults to 3 months). Adds notification options for post author through the [Workflows plugin](https://github.com/humanmade/Workflows).

## Add content expiration support to a post type

In order for site administrators be able to set workflows from the admin UI, post type support should be declared in a `init` action hook with priority between 2 and 98.

```
add_action( 'init', 'add_content_expiration_support', 5 );

/**
 * Add content expiration support to `post` post type.
 *
 * @return void
 */
function add_content_expiration_support() {
	add_post_type_support( 'post', 'content-expiration' );
}
```

## Filter the content expiration period

```
add_filter( 'content_expiration.expiration_period', 'content_expiration_period', 10, 3 );

/**
 * Filters the content expiration period.
 *
 * The format of the returned value should be a valid interval
 * specification for `DateInterval::__construct`.
 *
 * @param string $period Expiration period.
 * @param string $post_type Post type.
 * @param int $post_id Post ID.
 * @return string
 */
function content_expiration_period( $period, $post_type, $post_id ) {
	if ( $post_type === 'post' ) {
		$period = 'P1M';
	}

	return $period;
}
```

## Filter the labels in the posts list table

### Column heading

```
add_filter( 'content_expiration.admin.column_head', 'admin_column_head' );

/**
 * Filter the content expiration column heading.
 *
 * @param string $column_head Original column heading.
 * @return string
 */
function admin_column_head( $column_head ) {
	return __( 'Next Review', 'textdomain' );
}
```

### Column text before post expires

```
add_filter( 'content_expiration.admin.label_expires_on', __NAMESPACE__ . '\\expires_on_label' );

/**
 * Filter the column content label when the post is not expired.
 *
 * @param string $label Original label
 * @return string
 */
function expires_on_label( $label ) {
	return __( 'Review by', 'hm-playbook' );
}
```

### Column text after post has expired

```
add_filter( 'content_expiration.admin.expired_on', __NAMESPACE__ . '\\expired_on_label' );

/**
 * Filter the column content label when the post has already expired.
 *
 * @param string $label Original label
 * @return string
 */
function expired_on_label( $label ) {
	return __( 'Review overdue', 'hm-playbook' );
}
```
