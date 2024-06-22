Feature: Test the licence management WP-CLI commands.

  Scenario: licence get
    Given a WP install
    Given a plugin located at ./test-plugin

    When I try `wp test-plugin licence get`
    And STDERR should contain:
      """
      Error: The licence key has not been set. Use `wp test-plugin licence set-key {my-key}`.
      """