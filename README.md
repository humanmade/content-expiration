# Content Expiration

Content expiration plugin for the HM Playbook. Allows content of any post type to expire after set period, based on post modified time (defaults to 3 months). Adds notification options for post author through the [Wordflows plugin](https://github.com/humanmade/Workflows).

## Add content expiration support to a post type

```
add_action( 'init', 'add_content_expiration_support' );

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
add_filter( 'expiration_period', 'content_expiration_period', 10, 3 );

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
