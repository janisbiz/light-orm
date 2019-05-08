Feature: Connection

  Scenario: Connect to MySQL Database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I should have connection "light_orm_mysql"
