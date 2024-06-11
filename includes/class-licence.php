<?php
/**
 * POJO for the licence details themselves:
 * * the licence key, status, expiry date, last updated date.
 *
 * TODO: move the save function out of this class to allow deserialisation to work.
 * TODO: use the last updated time so if there is no communication to the licence server, it can be known if it's an old problem or maybe transient.
 *
 * @package brianhenryie/bh-wp-slswc-client
 */

namespace BrianHenryIE\WP_SLSWC_Client;

use DateTimeImmutable;
use DateTimeInterface;

class Licence implements \Serializable, \JsonSerializable {

	/**
	 * The licence key itself. Will be null until set.
	 */
	protected ?string $licence_key = null;

	/**
	 * The status. Enum TBD. TODO.
	 */
	protected string $status = 'invalid'; // 'empty'

	protected ?DateTimeInterface $expiry_date = null;

	protected ?DateTimeInterface $last_updated = null;

	/**
	 * The available license status types.
	 */
	public static function get_licence_statuses(): array {
		return array(
			'valid'           => __( 'Valid', 'bh-wp-slswc-client' ),
			'deactivated'     => __( 'Deactivated', 'bh-wp-slswc-client' ),
			'max_activations' => __( 'Max Activations reached', 'bh-wp-slswc-client' ),
			'invalid'         => __( 'Invalid', 'bh-wp-slswc-client' ),
			'inactive'        => __( 'Inactive', 'bh-wp-slswc-client' ),
			'active'          => __( 'Active', 'bh-wp-slswc-client' ),
			'expiring'        => __( 'Expiring', 'bh-wp-slswc-client' ),
			'expired'         => __( 'Expired', 'bh-wp-slswc-client' ),
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

	/**
	 * Serialize the object to an array.
	 *
	 * @used-by serialize()
	 */
	public function __serialize(): array {

		$arr         = get_object_vars( $this );
		$ordered_arr = array();
		foreach ( self::get_licence_object_schema_properties() as $property_name => $property_schema ) {
			if ( isset( $arr[ $property_name ] ) && $arr[ $property_name ] instanceof DateTimeInterface ) {
				$ordered_arr[ $property_name ] = $arr[ $property_name ]->format( \DateTimeInterface::ATOM );
				continue;
			}
			if ( isset( $arr[ $property_name ] ) ) {
				$ordered_arr[ $property_name ] = $arr[ $property_name ];
				continue;
			}
			if ( isset( $property_schema['type'] ) && is_array( $property_schema['type'] ) && in_array( 'null', $property_schema['type'], true ) ) {
				$ordered_arr[ $property_name ] = null;
			}
		}

		return $ordered_arr;
	}

	/**
	 * Given an array of the object's properties, set them.
	 *
	 * @used-by unserialize()
	 */
	public function __unserialize( array $data ): void {
		$this->licence_key  = $data['licence_key'];
		$this->status       = $data['status'] ?? $this->status;
		$this->expiry_date  = new DateTimeImmutable( $data['expires'] );
		$this->last_updated = new DateTimeImmutable( $data['last_updated'] );
	}


	/**
	 * @see Serializable::serialize()
	 */
	public function serialize() {
		return $this->jsonSerialize();
	}

	/**
	 * @see Serializable::unserialize()
	 */
	public function unserialize( string $data ) {
		$this->__unserialize( json_decode( $data, true ) );
	}

	/**
	 * @see \JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize() {
		return $this->__serialize();
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
				'description' => esc_html__( 'The licence key.', 'bh-wp-slswc-client' ),
				'type'        => 'string',
				// 'minimum'          => 1, // TODO: Is there a set length the key will be?
				// 'exclusiveMinimum' => true,
				// 'maximum'          => 3,
				// 'exclusiveMaximum' => true,
			),
			'status'        => array(
				'description' => esc_html__( 'The licence status.', 'bh-wp-slswc-client' ),
				'type'        => 'string',
				// 'enum' => array(
				// 'invalid',
				// ),
			),
			'last_updated'  => array(
				'description' => esc_html__( 'The last time the license server was successfully contacted.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'purchase_date' => array(
				'description' => esc_html__( 'The date of original purchase.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'order_link'    => array(
				'description' => esc_html__( 'A link to the original order domain.com/my-account/orders/123.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
			'expiry_date'   => array(
				'description' => esc_html__( 'The expiry date.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
			),
			'auto_renews'   => array(
				'description' => esc_html__( 'Will the licence auto-renew?', 'bh-wp-slswc-client' ),
				'type'        => array( 'boolean', 'null' ),
			),
			'renewal_link'  => array(
				'description' => esc_html__( 'A link to domain.com to renew the licence.', 'bh-wp-slswc-client' ),
				'type'        => array( 'string', 'null' ),
				'format'      => 'uri',
			),
		);
	}
}
