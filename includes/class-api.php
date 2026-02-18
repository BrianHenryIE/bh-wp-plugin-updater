<?php
/**
 * Should only run in background
 * Should trigger when the wp transient is set and manually edit it afterwards/ set its own transient for same time
 *
 * TODO: basically does error checking then calls the integration's similar functions and then caches
 * TODO: rate limit
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\WP_Plugin_Updater\Exception\Licence_Key_Not_Set_Exception;
use BrianHenryIE\WP_Plugin_Updater\Exception\Plugin_Updater_Exception;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory_Interface;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Headers;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\WP_Includes\Cron;
use Composer\Semver\Comparator;
use DateTimeImmutable;
use JsonMapper\JsonMapperInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @phpstan-import-type Licence_Update_Array from Licence
 */
class API implements API_Interface {
	use LoggerAwareTrait;

	protected Integration_Interface $service;

	protected Licence $licence;

	public function __construct(
		protected Settings_Interface $settings,
		LoggerInterface $logger,
		protected JsonMapperInterface $json_mapper,
		?Integration_Factory_Interface $integration_factory = null,
	) {
		$this->setLogger( $logger );

		try {
			$this->licence = $this->get_licence_details( false );
		} catch ( Licence_Key_Not_Set_Exception ) {
			$this->licence = new Licence();
		}

		$this->service = ( $integration_factory ?? new Integration_Factory( $logger ) )
							->get_integration( $settings );
	}

	/**
	 * Set the licence key without activating it.
	 *
	 * Deactivates existing licence key if present.
	 *
	 * @param string $license_key
	 *
	 * @throws Plugin_Updater_Exception If failing to deactivate the existing licence.
	 */
	public function set_license_key( string $license_key ): Licence {

		$existing_key = $this->licence->licence_key;
		if ( $existing_key === $license_key ) {
			return $this->licence;
		}
		if ( ! empty( $existing_key ) ) {
			if ( $this->licence->status === 'active' ) {
				$this->service->deactivate_licence( $this->licence );
			}
		}

		// TODO: Set the status to unknown?

		return $this->save_licence_information( $this->licence, array( 'licence_key' => $license_key ) );
	}

	/**
	 * Get the licence information, maybe cached, maybe remote, maybe an empty Licence object.
	 *
	 * @param bool|null $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 *
	 * @throws Licence_Key_Not_Set_Exception
	 */
	public function get_licence_details( ?bool $refresh = null ): Licence {

		// TODO: refresh should never be true on a page-load.

		// TODO: Do not continuously retry.

		return match ( $refresh ) {
			true => $this->service->refresh_licence_details( $this->licence ),
			false => $this->get_saved_licence_information() ?? new Licence(), // throw new Licence_Key_Not_Set_Exception(),
			default => $this->get_saved_licence_information() ?? $this->service->refresh_licence_details( $this->licence ),
		};
	}

	/**
	 * Parse the value of `get_option()` to the class requested.
	 *
	 * @template T of object
	 * @param string          $option_name
	 * @param class-string<T> $class_name
	 * @return ?T
	 */
	protected function get_option( string $option_name, string $class_name ): ?object {

		$value = get_option( $option_name );

		if ( empty( $value ) ) {
			$this->logger->debug( "No data saved in wp-options for {$option_name} ($class_name)." );
			return null;
		}

		if ( ! is_string( $value ) ) {
			$invalid_raw_value = function ( string $option_name ): string {
				$all_options = wp_load_alloptions();
				return $all_options[ $option_name ] ?? 'error finding value for log message';
			};
			$this->logger->warning(
				"Invalid data saved in wp-options for {$option_name} ($class_name): " . $invalid_raw_value( $option_name )
			);
			return null;
		}

		try {
			return $this->json_mapper->mapToClassFromString( $value, $class_name );
		} catch ( Throwable $e ) {
			$this->logger->error(
				"Failed to unserialize wp-options data for {$option_name} ($class_name) : " . $e->getMessage(),
				array(
					'exception'        => $e,
					'wp_options_value' => $value,
				)
			);
			return null;
		}
	}

	/**
	 * Get the licence information from the WordPress options database table. Verifies it is a Licence object.
	 */
	protected function get_saved_licence_information(): ?Licence {
		return $this->get_option(
			$this->settings->get_licence_data_option_name(),
			Licence::class
		);
	}

	/**
	 *
	 *
	 * @param Licence              $licence
	 * @param Licence_Update_Array $updates
	 */
	protected function save_licence_information( Licence $licence, array $updates = array() ): Licence {

		// TODO: test does the order of the array keys matter.
		/** @phpstan-ignore-next-line argument.type */
		$updated_licence = new Licence(
			...( array_merge(
				(array) $licence,
				$updates,
				array( 'last_updated' => new DateTimeImmutable() )
			) )
		);

		update_option(
			$this->settings->get_licence_data_option_name(),
			wp_json_encode( $updated_licence )
		);

		return $updated_licence;
	}

	/**
	 * Send a HTTP request to deactivate the licence from this site.
	 *
	 * Is this a good idea? Should it only be possible from the licence server?
	 *
	 * https://updatestest.bhwp.ie/wp-json/slswc/v1/deactivate?slug=a-plugin
	 *
	 * @throws Plugin_Updater_Exception
	 */
	public function deactivate_licence(): Licence {

		if ( is_null( $this->licence->licence_key ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$licence = $this->service->deactivate_licence( $this->licence );

		// Set last updated. (should do that in
		return $this->save_licence_information( $licence );
	}

	/**
	 * Activate the licence on this site.
	 *
	 * @throws Plugin_Updater_Exception
	 */
	public function activate_licence(): Licence {

		if ( is_null( $this->licence->licence_key ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$licence = $this->service->activate_licence( $this->licence );

		// TODO: Let's record "last successfully updated" as well as "last updated". (or use a rate limiter)

		$this->save_licence_information( $licence );

		return $this->licence;
	}

	/**
	 * Product information should be available regardless of licence status.
	 *
	 * Get the remote product information for the {@see get_plugins()} information array.
	 *
	 * null when first run and no cached product information.
	 */
	public function get_plugin_information( ?bool $refresh = null ): ?Plugin_Info {

		if ( true !== $refresh ) {
			// TODO: Add a background task to refresh the product information.
			// TODO: Check the last time it was refreshed and rate limit the refreshing.
		}

		$result = match ( $refresh ) {
			true => $this->get_remote_product_information(),
			false => $this->get_cached_product_information(),
			default => $this->get_cached_product_information() ?? $this->get_remote_product_information(),
		};

		if ( is_null( $result ) && $refresh === false ) {
			$this->logger->info( 'Cache was empty, scheduling an immediate update' );
			$this->schedule_immediate_background_update();
		}

		return $result;
	}

	protected function get_remote_product_information(): ?Plugin_Info {

		$product = $this->service->get_remote_product_information( $this->licence );

		update_option(
			$this->settings->get_plugin_information_option_name(),
			wp_json_encode( $product )
		);

		return $product;
	}

	protected function get_cached_product_information(): ?Plugin_Info {

		return $this->get_option(
			$this->settings->get_plugin_information_option_name(),
			Plugin_Info::class
		);
	}

	/**
	 * Update information should be available regardless of licence status... alas, it is not.
	 *
	 * Get the remote product information for the {@see get_plugins()} information array.
	 *
	 * null when first run and no cached information.
	 */
	public function get_check_update( ?bool $refresh = null ): ?Plugin_Update {

		if ( true !== $refresh ) {
			// TODO: Add a background task to refresh the product information.
			// TODO: Check the last time it was refreshed and rate limit the refreshing.
		}

		return match ( $refresh ) {
			true => $this->get_remote_check_update(),
			false => $this->get_cached_check_update(),
			default => $this->get_cached_check_update() ?? $this->get_remote_check_update(),
		};
	}

	/**
	 * Schedule an immediate background update check.
	 *
	 * TODO: rate limit this.
	 */
	public function schedule_immediate_background_update(): void {
		$cron          = new Cron( $this, $this->settings, $this->logger );
		$cron_job_name = $cron->get_immediate_update_check_cron_job_name();
		wp_schedule_single_event( time(), $cron_job_name );
	}

	protected function get_remote_check_update(): ?Plugin_Update {

		try {
			$check_update = $this->service->get_remote_check_update( $this->licence );
			update_option(
				$this->settings->get_check_update_option_name(),
				wp_json_encode( $check_update )
			);
		} catch ( \Exception $e ) {
			$this->logger->error( $e->getMessage(), array( 'exception' => $e ) );
			return null;
		}

		return $check_update;
	}

	protected function get_cached_check_update(): ?Plugin_Update {
		return $this->get_option(
			$this->settings->get_check_update_option_name(),
			Plugin_Update::class
		);
	}

	/**
	 * Compare the currently installed version (or 0.0.0) with the available version.
	 */
	public function is_update_available( ?bool $refresh = null ): bool {
		return Comparator::greaterThan(
			$this->get_available_version( $refresh ) ?? '0.0.0',
			$this->get_current_version() ?? '0.0.0',
		);
	}

	protected function get_available_version( ?bool $refresh = null ): ?string {
		return $this->get_check_update( $refresh )->version ?? null;
	}

	protected function get_current_version(): ?string {
		$headers = Plugin_Headers::from_file( constant( 'WP_PLUGIN_DIR' ) . '/' . $this->settings->get_plugin_basename() );

		return $headers->version;
	}
}
