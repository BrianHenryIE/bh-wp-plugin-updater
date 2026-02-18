<?php
/**
 * Get a JsonMapper instance that can decode Money and DateTimeInterface.
 *
 * @package brianhenryie/bh-wp-plugin-updater
 */

namespace BrianHenryIE\WP_Plugin_Updater\Helpers\JsonMapper;

use JsonMapper\Exception\BuilderException;
use JsonMapper\Exception\ClassFactoryException;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\JsonMapperInterface;
use DateTimeInterface;

/**
 * @see JsonMapperBuilder::build()
 */
class JsonMapper_Helper {

	/**
	 * Get a JSONMapper instance configured with DateTimeInterface and Money helpers.
	 *
	 * @throws ClassFactoryException Something must be wrong with one of our factory implementations. Definitely should not be a run-time error to just register them.
	 * @throws BuilderException Would suggest something went wrong inside JsonMapper itself.
	 */
	public function build(): JsonMapperInterface {

		$factory_registry = new FactoryRegistry();

		$factory_registry->addFactory(
			DateTimeInterface::class,
			new JsonMapper_DateTimeInterface()
		);

		// TODO: after testing, see what -> are unnecessary.
		$property_mapper = new PropertyMapper( $factory_registry );
		$mapper          = JsonMapperBuilder::new()
			->withPropertyMapper( $property_mapper )
			->withAttributesMiddleware()
			->withDocBlockAnnotationsMiddleware()
			->withTypedPropertiesMiddleware()
			->withNamespaceResolverMiddleware()
			->withObjectConstructorMiddleware( $factory_registry )
			->build();

		return $mapper;
	}
}
