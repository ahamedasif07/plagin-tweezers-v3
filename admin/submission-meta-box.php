<?php
/** @var array $submission */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( empty( $submission ) || ! is_array( $submission ) ) {
	echo '<p>' . esc_html__( 'No submission data found.', 'ttq' ) . '</p>';
	return;
}
?>
<table class="widefat striped">
	<tbody>
		<?php foreach ( $submission as $key => $value ) : ?>
			<tr>
				<th style="width:200px;"><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></th>
				<td>
					<?php
					if ( is_array( $value ) ) {
						echo esc_html( implode( ', ', $value ) );
					} elseif ( 'logo_url' === $key && ! empty( $value ) ) {
						echo '<img src="' . esc_url( $value ) . '" style="max-width:120px;" />';
					} else {
						echo esc_html( $value );
					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
