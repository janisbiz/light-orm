Feature: Repository Create

  Scenario: Test
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    Then I call method "create" with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |
