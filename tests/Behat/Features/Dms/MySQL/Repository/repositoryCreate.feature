Feature: Repository Create

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I reset database for connection "light_orm_mysql"

  Scenario: Create rows in table "test_table_one"
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "create" with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |
    Then I call method "read" which will return following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |

  Scenario: Create rows in table "test_table_two"
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    When I call method "create" with parameters:
      | id |
      | 1  |
      | 2  |
    Then I call method "read" which will return following rows:
      | id |
      | 1  |
      | 2  |

  Scenario: Create rows in table "test_table_one_two"
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    When I call method "create" with parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    Then I call method "read" which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
