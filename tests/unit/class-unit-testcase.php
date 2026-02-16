<?php

namespace BrianHenryIE\WP_Plugin_Updater;

use BrianHenryIE\ColorLogger\ColorLogger;
use Codeception\Test\Unit;
use Psr\Log\LoggerInterface;
use WP_Mock;

class Unit_Testcase extends Unit {

	protected LoggerInterface $logger;

	protected function setUp(): void {
		WP_Mock::setUp();

		$this->logger = new ColorLogger();
	}

	protected function tearDown(): void {
		parent::_tearDown();
		WP_Mock::tearDown();
		\Patchwork\restoreAll();
	}
}
