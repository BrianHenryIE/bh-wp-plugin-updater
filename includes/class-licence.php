<?php
/**
 * POJO for the licence details themselves:
 * * the licence key, status, expiry date, last updated date.
 *
 * TODO: use the last updated time so if there is no communication to the licence server, it can be known if it's an old problem or maybe transient.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use DateTimeInterface;

/**
 * @phpstan-type Licence_Update_Array array{licence_key?:string|null, status?:string, expiry_date?:DateTimeInterface|null, last_updated?:DateTimeInterface|null, purchase_date?:DateTimeInterface|null, order_link?:string|null, auto_renews?:bool|null, renewal_link?:string|null}
 */
class Licence {

	/**
	 * Constructor.
	 *
	 * @param ?string            $licence_key
	 * @param string             $status
	 * @param ?DateTimeInterface $expiry_date
	 * @param ?DateTimeInterface $last_updated
	 * @param ?DateTimeInterface $purchase_date
	 * @param ?string            $order_link
	 * @param ?bool              $auto_renews
	 * @param ?string            $renewal_link
	 */
	public function __construct(
		/**
		 * The licence key itself. Will be null until set.
		 *
		 * @var string $licence_key
		 */
		public readonly ?string $licence_key = null,
		/**
		 * The status. Enum TBD. TODO.
		 *
		 * @var string $status
		 */
		public readonly string $status = 'unknown',// 'empty'
		/**
		 * The date the licence did or will expire
		 *
		 * @var DateTimeInterface|null
		 */
		public readonly ?DateTimeInterface $expiry_date = null,
		/**
		 * The last time the license server was successfully contacted.
		 *
		 * @var DateTimeInterface|null
		 */
		public readonly ?DateTimeInterface $last_updated = null,
		public readonly ?DateTimeInterface $purchase_date = null,
		/**
		 * A link to the original order domain.com/my-account/orders/123
		 *
		 * @var string|null
		 */
		public readonly ?string $order_link = null,
		/**
		 * Will the licence auto-renew?
		 *
		 * @var bool|null
		 */
		public readonly ?bool $auto_renews = null,
		/**
		 * A link to domain.com to renew the licence.
		 *
		 * @var string|null
		 */
		public readonly ?string $renewal_link = null,
	) {
	}


	/**
	 * The available license status types.
	 */
	public static function get_licence_statuses(): array {
		return array(
			'valid'           => __( 'Valid', 'bh-wp-plugin-updater' ),
			'deactivated'     => __( 'Deactivated', 'bh-wp-plugin-updater' ),
			'max_activations' => __( 'Max Activations reached', 'bh-wp-plugin-updater' ),
			'invalid'         => __( 'Invalid', 'bh-wp-plugin-updater' ),
			'inactive'        => __( 'Inactive', 'bh-wp-plugin-updater' ),
			'active'          => __( 'Active', 'bh-wp-plugin-updater' ),
			'expiring'        => __( 'Expiring', 'bh-wp-plugin-updater' ),
			'expired'         => __( 'Expired', 'bh-wp-plugin-updater' ),
		);
	}

	public function is_active() {
		return 'active' === $this->status;

		// array(
		// 'active',
		// 'expiring',
		// 'expired',
		// );
	}

	/**
	 * @return array<string,array{description:string,type:string|array<string>,format:string}>
	 */
	public static function get_licence_object_schema_properties(): array {
		return array(
			'licence_key'   => array(
				'description' => esc_html__( 'The licence key.', 'bh-wp-plugin-updater' ),
				'type'        => 'string',
				// 'minimum'          => 1, // TODO: Is there a set length the key will be?
				// 'exclusiveMinimum' => true,
				// 'maximum'          => 3,
				// 'exclusiveMaximum' => true,
			),
			'status'        => array(
				'description' => esc_html__( 'The licence status.', 'bh-wp-plugin-updater' ),
				'type'        => 'string',
				// 'enum' => array(
				// 'invalid',
				// ),
			),
			'last_updated'  => array(
				'description' => esc_html__( 'The last time the license server was successfully contacted.', 'bh-wp-plugin-updater' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'purchase_date' => array(
				'description' => esc_html__( 'The date of original purchase.', 'bh-wp-plugin-updater' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'order_link'    => array(
				'description' => esc_html__( 'A link to the original order domain.com/my-account/orders/123.', 'bh-wp-plugin-updater' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
			'expiry_date'   => array(
				'description' => esc_html__( 'The expiry date.', 'bh-wp-plugin-updater' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'auto_renews'   => array(
				'description' => esc_html__( 'Will the licence auto-renew?', 'bh-wp-plugin-updater' ),
				'type'        => array( 'boolean', 'null' ),
			),
			'renewal_link'  => array(
				'description' => esc_html__( 'A link to domain.com to renew the licence.', 'bh-wp-plugin-updater' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
		);
	}
}
