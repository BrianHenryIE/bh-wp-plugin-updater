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

		$allowed_fields  = array( 'licence_key', 'status', 'expires', 'last_updated' );
		$datetime_fields = array( 'expires', 'last_updated' );

		$arr = get_object_vars( $this );
		foreach ( $datetime_fields as $datetime_field ) {
			if ( isset( $arr[ $datetime_field ] ) && $arr[ $datetime_field ] instanceof DateTimeInterface ) {
				$arr[ $datetime_field ] = $arr[ $datetime_field ]->format( \DateTimeInterface::ATOM );
			}
		}

		return array_intersect_key( $arr, array_flip( $allowed_fields ) );
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
}
