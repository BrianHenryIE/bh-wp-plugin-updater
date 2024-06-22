Feature: Test the licence management WP-CLI commands.

  Scenario: licence get
    Given a WP install
    Given a plugin located at ./test-plugin

    When I try `wp test-plugin licence get`
    Then STDERR should contain:
      """
      Error: The licence key has not been set. Use `wp test-plugin licence set-key {my-key}`.
      """

  Scenario: licence set
    Given a WP install
    Given a plugin located at ./test-plugin

    When I run `wp test-plugin licence set-key abcdefghijklmnopqrstuvwxyz`
    Then STDERR should be empty
    And STDOUT should contain:
      """
      Success: Licence key set to: abcdefghijklmnopqrstuvwxyz
      """
    When I run `wp test-plugin licence get --format=json`
    Then STDERR should be empty
    And STDOUT should be JSON containing:
      """
      [{"licence_key":"abcdefghijklmnopqrstuvwxyz","status":"unknown"}]
      """

  Scenario: licence activate
    Given a WP install
    Given a plugin located at ./test-plugin

    When I run `wp test-plugin licence set-key abcdefghijklmnopqrstuvwxyz`
    Then STDERR should be empty
    And STDOUT should contain:
      """
      Success: Licence key set to: abcdefghijklmnopqrstuvwxyz
      """
    When I run `wp test-plugin licence get --format=json`
    Then STDERR should be empty
    And STDOUT should be JSON containing:
      """
      [{"licence_key":"abcdefghijklmnopqrstuvwxyz","status":"unknown"}]
      """

    Given a request to wp-json/slswc/v1/activate? responds with tests/_data/features/activate-success.php

    When I run `wp test-plugin licence activate`
    Then STDERR should be empty
    And STDOUT should contain:
      """
      Success: active
      """
