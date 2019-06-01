Feature: Repository Delete

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I reset database for connection "light_orm_mysql"

  Scenario: Create and delete rows in table "test_table_one"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |
      | 2      | 3                 | varcharNotNull2          | 3.3                   | 4               | varcharNull2           | 4.4                 | 2019-01-02 00:00:00 |                   |
    When I call method "deleteRow" on repository with parameters:
      | id:int |
      | 2      |
    Then I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |

  Scenario: Create and delete rows in table "test_table_one" with existing entities
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 2      | 3                 | varcharNotNull2          | 3.3                   | 4               | varcharNull2           | 4.4                 | 2019-01-02 00:00:00 |                   |
      | 3      | 5                 | varcharNotNull3          | 5.5                   | 6               | varcharNull3           | 6.6                 | 2019-01-04 00:00:00 |                   |
    When I call method "deleteEntity" on repository with existing entities
    Then I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |

  Scenario: Create and delete rows in table "test_table_one" with exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id:int |
      | 1      |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "create" on repository with parameters:
      | testTableOneId:int | testTableTwoId:int |
      | 1                  | 1                  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "deleteRow" on repository with parameters and expecting exception:
      | id:int |
      | 1      |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`light_orm_mysql`.`test_table_one_two`, CONSTRAINT `fk_test_table_one_two_test_table_on_id` FOREIGN KEY (`test_table_one_id`) REFERENCES `test_table_one` (`id`) ON DELETE NO ACTION ON UPDATE N)"

  Scenario: Delete rows in tables "test_table_one_two", "test_table_one", "test_table_two" when in transaction with rollback
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId:int | testTableTwoId:int |
      | 1                  | 1                  |
    And I call method "deleteEntity" on repository with existing entities
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |
    And I call method "deleteEntity" on repository with existing entities
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id:int |
      | 1      |
    And I call method "deleteEntity" on repository with existing entities
    When I rollback transaction on connection "light_orm_mysql"
    Then I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | testTableOneId:int | testTableTwoId:int |
      | 1                  | 1                  |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | id:int |
      | 1      |

  Scenario: Delete rows in tables "test_table_one_two", "test_table_one", "test_table_two" when in transaction
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId:int | testTableTwoId:int |
      | 1                  | 1                  |
    And I call method "deleteEntity" on repository with existing entities
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string   | updatedAt:?string |
      | 1      | 1                 | varcharNotNull1          | 1.1                   | 2               | varcharNull1           | 2.2                 | 2019-01-01 00:00:00 |                   |
    And I call method "deleteEntity" on repository with existing entities
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id:int |
      | 1      |
    And I call method "deleteEntity" on repository with existing entities
    When I commit transaction on connection "light_orm_mysql"
    Then I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | testTableOneId:int | testTableTwoId:int |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | id:int | intColNotNull:int | varcharColNotNull:string | floatColNotNull:float | intColNull:?int | varcharColNull:?string | floatColNull:?float | createdAt:?string | updatedAt:?string |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | id:int |
