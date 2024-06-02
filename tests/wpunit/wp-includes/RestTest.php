<?php

namespace BrianHenryIE\WP_SLSWC_Client\WP_Includes;

use BrianHenryIE\WP_SLSWC_Client\API_Interface;
use BrianHenryIE\WP_SLSWC_Client\Server\Product;
use BrianHenryIE\WP_SLSWC_Client\Settings;
use BrianHenryIE\WP_SLSWC_Client\Settings_Interface;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;

class RestTest extends \lucatume\WPBrowser\TestCase\WPRestApiTestCase
{
    /**
     * @var \WpunitTester
     */
    protected $tester;
    
    public function setUp() :void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
    }

    public function tearDown() :void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

//    public function test_getting_posts() :void
//    {
//        // Create an editor.
//        $author_id = static::factory()->user->create( [ 'role' => 'author' ] );
//
//        // Create and become editor.
//        $editor_id = static::factory()->user->create( [ 'role' => 'editor' ] );
//        wp_set_current_user( $editor_id );
//
//        // Create 2 posts, one from the editor and one from the author.
//        $post_1_id = static::factory()->post->create( [ 'post_author' => $editor_id ] );
//		$post_2_id = static::factory()->post->create( [ 'post_author' => $author_id ] );
//
//		// Get all posts in the database.
//		$request = new \WP_REST_Request( 'GET', '/wp/v2/posts' );
//		$request->set_param( 'per_page', 10 );
//		$response = rest_get_server()->dispatch( $request );
//		$this->assertSame( 200, $response->get_status() );
//		$this->assertCount( 2, $response->get_data() );
//
//		// Exclude editor and author.
//		$request = new \WP_REST_Request( 'GET', '/wp/v2/posts' );
//		$request->set_param( 'per_page', 10 );
//		$request->set_param( 'author_exclude', [ $editor_id, $author_id ] );
//		$response = rest_get_server()->dispatch( $request );
//		$this->assertSame( 200, $response->get_status() );
//		$data = $response->get_data();
//		$this->assertCount( 0, $data );
//
//		// Exclude editor.
//		$request = new \WP_REST_Request( 'GET', '/wp/v2/posts' );
//		$request->set_param( 'per_page', 10 );
//		$request->set_param( 'author_exclude', $editor_id );
//		$response = rest_get_server()->dispatch( $request );
//		$this->assertSame( 200, $response->get_status() );
//		$data = $response->get_data();
//		$this->assertCount( 1, $data );
//		$this->assertNotEquals( $editor_id, $data[0]['author'] );
//
//		// Invalid 'author_exclude' should error.
//		$request = new \WP_REST_Request( 'GET', '/wp/v2/posts' );
//		$request->set_param( 'author_exclude', 'invalid' );
//		$response = rest_get_server()->dispatch( $request );
//		$this->assertErrorResponse( 'rest_invalid_param', $response, 400 );
//    }

	public function test_get_product_information(): void {

		$settings = \Mockery::mock(Settings_Interface::class)->makePartial();
		$settings->shouldReceive('get_rest_base')->andReturn('a-plugin');

		$api = \Mockery::mock(API_Interface::class)->makePartial();
		$rest = new Rest( $api, $settings );

		add_action( 'rest_api_init', array( $rest, 'register_routes' ) );
		do_action( 'rest_api_init' );

		$product = $this->get_product();

		$api->shouldReceive('get_product_information')->andReturn($product);

		$request = new \WP_REST_Request( 'GET', '/a-plugin/v1/licence-product' );

		$response = rest_get_server()->dispatch( $request );


		$this->assertSame('a-plugin', $response->get_data()->software_slug);
	}

	protected function get_product(): Product {

		$product_json = <<<JSON
{
 "software": 1,
 "software_type": "plugin",
 "allow_staging": "yes",
 "renewal_period": "annual",
 "software_slug": "a-plugin",
 "version": "",
 "author": "",
 "required_wp": "",
 "compatible_to": "",
 "updated": "",
 "activations": "1",
 "staging_activations": "3",
 "description": "",
 "change_log": "",
 "installation": "",
 "documentation_link": "",
 "banner_low": "",
 "banner_high": "",
 "update_file_id": "40bb2001-48c3-4633-995a-447aa82b491d",
 "update_file_url": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
 "update_file_name": "bh-wp-autologin-urls.2.3.0-alozbb.zip",
 "update_file": {
 "id": "40bb2001-48c3-4633-995a-447aa82b491d",
 "file": "https:\/\/updatestest.bhwp.ie\/wp-content\/uploads\/woocommerce_uploads\/2024\/05\/bh-wp-autologin-urls.2.3.0-alozbb.zip",
 "name": "bh-wp-autologin-urls.2.3.0-alozbb.zip"
 },
 "thumbnail": false
 }
JSON;



		$factoryRegistry = new FactoryRegistry();
		$mapper          = JsonMapperBuilder::new()
		                                    ->withDocBlockAnnotationsMiddleware()
		                                    ->withObjectConstructorMiddleware( $factoryRegistry )
		                                    ->withPropertyMapper( new PropertyMapper( $factoryRegistry ) )
		                                    ->withTypedPropertiesMiddleware()
		                                    ->withNamespaceResolverMiddleware()
		                                    ->build();

		return $mapper->mapToClassFromString( $product_json, Product::class );


	}
}
