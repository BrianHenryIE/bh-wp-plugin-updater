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
use BrianHenryIE\WP_Plugin_Updater\Exception\Plugin_Updater_Exception_Abstract;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Factory_Interface;
use BrianHenryIE\WP_Plugin_Updater\Integrations\Integration_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Info_Interface;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update;
use BrianHenryIE\WP_Plugin_Updater\Model\Plugin_Update_Interface;
use DateTimeImmutable;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class API implements API_Interface {
	use LoggerAwareTrait;

	protected Integration_Interface $service;

	protected Licence $licence;

	public function __construct(
		protected Settings_Interface $settings,
		LoggerInterface $logger,
		?Integration_Factory_Interface $integration_factory = null
	) {
		$this->setLogger( $logger );

		try {
			$this->licence = $this->get_licence_details( false );
		} catch ( Licence_Key_Not_Set_Exception $e ) {
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
	 * @throws Plugin_Updater_Exception_Abstract If failing to deactivate the existing licence.
	 */
	public function set_license_key( string $license_key ): Licence {

		$existing_key = $this->licence->get_licence_key();
		if ( $existing_key === $license_key ) {
			return $this->licence;
		}
		if ( ! empty( $existing_key ) ) {
			if ( $this->licence->get_status() === 'active' ) {
				$this->service->deactivate_licence( $this->licence );
			}
		}

		// TODO: Set the status to unknown?

		$this->licence->set_licence_key( $license_key );
		$this->save_licence_information( $this->licence );

		return $this->licence;
	}

	/**
	 * Get the licence information, maybe cached, maybe remote, maybe an empty Licence object.
	 *
	 * @param bool|null $refresh True: force refresh from API; false: do not refresh; null: use cached value or refresh if missing.
	 *
	 * @throws Licence_Key_Not_Set_Exception
	 */
	public function get_licence_details( ?bool $refresh = null ): Licence {

		// TODO: refresh should never be true on a pageload.

		// TODO: Do not continuously retry.

		return match ( $refresh ) {
			true => $this->service->refresh_licence_details( $this->licence ),
			false => $this->get_saved_licence_information() ?? new Licence(), // throw new Licence_Key_Not_Set_Exception(),
			default => $this->get_saved_licence_information() ?? $this->service->refresh_licence_details( $this->licence ),
		};
	}

	/**
	 * Get the licence information from the WordPress options database table. Verifies it is a Licence object.
	 */
	protected function get_saved_licence_information(): ?Licence {
		// TODO: try / catch a malformed serialized object.
		// TODO: serialize to an array, not to a serialized object, so the class can be changed in future.
		$value = get_option(
			$this->settings->get_licence_data_option_name(),
			null
		);
		if ( is_null( $value ) ) {
			$this->logger->debug( 'No licence information found in wp-options.' );
			return null;
		}
		try {
			$licence = new Licence();
			$licence->__unserialize( $value );
			return $licence;
		} catch ( \Throwable $e ) {
			$this->logger->error( 'Failed to unserialize licence information: ' . $e->getMessage(), array( 'value' => $value ) );
			return null;
		}
	}

	/**
	 *
	 *
	 * @param Licence $licence
	 *
	 * @return void
	 */
	protected function save_licence_information( Licence $licence ): void {
		$licence->set_last_updated( new DateTimeImmutable() );

		update_option(
			$this->settings->get_licence_data_option_name(),
			$licence->__serialize()
		);
	}

	/**
	 * Send a HTTP request to deactivate the licence from this site.
	 *
	 * Is this a good idea? Should it only be possible from the licence server?
	 *
	 * https://updatestest.bhwp.ie/wp-json/slswc/v1/deactivate?slug=a-plugin
	 *
	 * @throws Plugin_Updater_Exception_Abstract
	 */
	public function deactivate_licence(): Licence {

		if ( is_null( $this->licence->get_licence_key() ) ) {
			throw new Licence_Key_Not_Set_Exception();
		}

		$licence = $this->service->deactivate_licence( $this->licence );

		// TODO: save
		$licence->set_last_updated( new DateTimeImmutable() );

		return $licence;
	}

	/**
	 * Activate the licence on this site.
	 *
	 * @throws Plugin_Updater_Exception_Abstract
	 */
	public function activate_licence(): Licence {

		if ( is_null( $this->licence->get_licence_key() ) ) {
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
	public function get_plugin_information( ?bool $refresh = null ): ?Plugin_Info_Interface {

		if ( true !== $refresh ) {
			// TODO: Add a background task to refresh the product information.
			// TODO: Check the last time it was refreshed and rate limit the refreshing.
		}

		return match ( $refresh ) {
			true => $this->get_remote_product_information(),
			false => $this->get_cached_product_information(),
			default => $this->get_cached_product_information() ?? $this->get_remote_product_information(),
		};
	}

	protected function get_remote_product_information(): ?Plugin_Info_Interface {

		$product = $this->service->get_remote_product_information( $this->licence );

		update_option( $this->settings->get_plugin_information_option_name(), $product );

		return $product;
	}

	protected function get_cached_product_information(): ?Plugin_Info_Interface {
		$cached_product_information = get_option(
			// plugin_slug_plugin_information
			$this->settings->get_plugin_information_option_name(),
			null
		);
		if ( $cached_product_information instanceof Plugin_Info_Interface ) {
			$this->logger->debug( 'returning cached product information for ' . $cached_product_information->get_software_slug() );
			return $cached_product_information;
		}
		$this->logger->debug( 'product not found in cache: ' . $this->settings->get_plugin_slug() );
		return null;
	}

	/**
	 * Update information should be available regardless of licence status... alas, it is not.
	 *
	 * Get the remote product information for the {@see get_plugins()} information array.
	 *
	 * null when first run and no cached information.
	 */
	public function get_check_update( ?bool $refresh = null ): ?Plugin_Update_Interface {

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

	protected function get_remote_check_update(): ?Plugin_Update_Interface {

		$check_update = $this->service->get_remote_check_update( $this->licence );

		update_option( $this->settings->get_check_update_option_name(), $check_update->__serialize() );

		return $check_update;
	}

	protected function get_cached_check_update(): ?Plugin_Update_Interface {
		$cached_check_update = get_option(
			$this->settings->get_check_update_option_name(),
			null
		);

		$factory_registry = new FactoryRegistry();
		$mapper           = JsonMapperBuilder::new()
											->withObjectConstructorMiddleware( $factory_registry )
											->withPropertyMapper( new PropertyMapper( $factory_registry ) )
											->build();

		$mapped_product_updated = $mapper->mapToClassFromString(
			json_encode( $cached_check_update ),
			Plugin_Update::class
		);

		if ( $mapped_product_updated instanceof Plugin_Update_Interface ) {
			$this->logger->debug( 'returning cached check_update for ' . $this->settings->get_plugin_slug() );
			return $mapped_product_updated;
		}
		$this->logger->debug( 'check_update Plugin_Update_Interface not found in cache: ' . $this->settings->get_plugin_slug() );
		return null;
	}

	/**
	 * TODO: semver compare.
	 */
	public function is_update_available( ?bool $refresh = null ): bool {
		return version_compare(
			$this->get_check_update( $refresh )?->get_version() ?? '0.0.0',
			get_plugins()[ $this->settings->get_plugin_basename() ]['Version'] ?? '0.0.0',
			'>'
		);
	}
}
