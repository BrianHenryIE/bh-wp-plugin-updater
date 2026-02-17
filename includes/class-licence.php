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

use DateTimeImmutable;
use DateTimeInterface;
use SimplePie\Parse\Date;

class Licence {

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

	/**
	 * Get the licence key itself.
	 */
	public function get_licence_key() {
		return $this->licence_key;
	}

	/**
	 * Set the licence key
	 *
	 * @param string $licence_key licence key.
	 */
	public function set_licence_key( string $licence_key ): void {
		$this->licence_key = $licence_key;
	}

	/**
	 * Get the licence status.
	 */
	public function get_status(): string {
		return $this->status;
	}

	/**
	 * Set the licence status
	 *
	 * @param string $status licence status.
	 */
	public function set_status( string $status ): void {
		$this->status = $status;
	}

	/**
	 * Get the licence expiry date.
	 */
	public function get_expiry_date(): ?DateTimeInterface {
		return $this->expiry_date;
	}

	/**
	 * Set the licence expires date.
	 *
	 * @param DateTimeInterface $expiry_date licence expiry date.
	 */
	public function set_expiry_date( DateTimeInterface $expiry_date ): void {
		$this->expiry_date = $expiry_date;
	}

	public function get_last_updated(): ?DateTimeInterface {
		return $this->last_updated;
	}

	public function set_last_updated( ?DateTimeInterface $last_updated ): void {
		$this->last_updated = $last_updated;
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
