Feature: Repository Update

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I reset database for connection "light_orm_mysql"

  Scenario: Create and update rows in table "test_table_one"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull1   | 1.1             | 2          | varcharNull1   | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |
    When I call method "updateRow" on repository with parameters:
      | id | varcharColNotNull      | varcharColNull      | updatedAt           |
      | 1  | varcharNotNull1Updated | varcharNull1Updated | 2019-01-03 00:00:00 |
      | 2  | varcharNotNull2Updated | varcharNull2Updated | 2019-01-03 00:00:00 |
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull      | floatColNotNull | intColNull | varcharColNull      | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull1Updated | 1.1             | 2          | varcharNull1Updated | 2.2          | 2019-01-01 00:00:00 | 2019-01-03 00:00:00 |
      | 2  | 3             | varcharNotNull2Updated | 3.3             | 4          | varcharNull2Updated | 4.4          | 2019-01-02 00:00:00 | 2019-01-03 00:00:00 |

  Scenario: Create and update rows in tables "test_table_two", "test_table_one_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      | 1  |
      | 2  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "create" on repository with parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    When I call method "updateRow" on repository with parameters:
      | testTableOneIdCurrent | testTableTwoIdCurrent | testTableOneId | testTableTwoId |
      | 1                     | 2                     | 1              | 1              |
      | 2                     | 1                     | 2              | 2              |
    Then I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 2              | 2              |

  Scenario: Update rows in table "test_table_one_two" with exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    When I call method "updateRow" on repository with parameters and expecting exception:
      | testTableOneIdCurrent | testTableTwoIdCurrent | testTableOneId | testTableTwoId |
      | 1                     | 1                     | 0              | 0              |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`light_orm_mysql`.`test_table_one_two`, CONSTRAINT `fk_test_table_one_two_test_table_on_id` FOREIGN KEY (`test_table_one_id`) REFERENCES `test_table_one` (`id`) ON DELETE NO ACTION ON UPDATE N)"

  Scenario: Update rows in table "test_table_one_two" with existing entities
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 2              | 2              |
    When I call method "updateEntity" on repository with existing entities and parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    Then I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |

  Scenario: Update rows in table "test_table_one_two" with existing entities and exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    When I call method "updateEntity" on repository with existing entities and parameters and expecting exception:
      | testTableOneId | testTableTwoId |
      | 0              | 0              |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`light_orm_mysql`.`test_table_one_two`, CONSTRAINT `fk_test_table_one_two_test_table_on_id` FOREIGN KEY (`test_table_one_id`) REFERENCES `test_table_one` (`id`) ON DELETE NO ACTION ON UPDATE N)"

  Scenario: Insert and update rows in tables "test_table_one", "test_table_one_two" when in transaction with rollback
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    And I call method "updateEntity" on repository with existing entities and parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 2              | 2              |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 3  | 5             | varcharNotNull3   | 5.5             | 6          | varcharNull3   | 6.6          | 2019-01-03 01:00:00 |           |
    And I call method "updateRow" on repository with parameters:
      | id | varcharColNotNull                 | varcharColNull                 | updatedAt           |
      | 1  | varcharNotNull1UpdatedTransaction | varcharNull1UpdatedTransaction | 2019-01-03 01:00:00 |
      | 2  | varcharNotNull2UpdatedTransaction | varcharNull2UpdatedTransaction | 2019-01-03 01:00:00 |
      | 3  | varcharNotNull3UpdatedTransaction | varcharNull3UpdatedTransaction | 2019-01-03 01:00:00 |
    When I rollback transaction on connection "light_orm_mysql"
    Then I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository" as active repository
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull      | floatColNotNull | intColNull | varcharColNull      | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull1Updated | 1.1             | 2          | varcharNull1Updated | 2.2          | 2019-01-01 00:00:00 | 2019-01-03 00:00:00 |
      | 2  | 3             | varcharNotNull2Updated | 3.3             | 4          | varcharNull2Updated | 4.4          | 2019-01-02 00:00:00 | 2019-01-03 00:00:00 |

  Scenario: Insert and update rows in tables "test_table_one", "test_table_one_two" when in transaction
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    And I call method "updateEntity" on repository with existing entities and parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 2              | 2              |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 3  | 5             | varcharNotNull3   | 5.5             | 6          | varcharNull3   | 6.6          | 2019-01-03 01:00:00 |           |
    And I call method "updateRow" on repository with parameters:
      | id | varcharColNotNull                 | varcharColNull                 | updatedAt           |
      | 1  | varcharNotNull1UpdatedTransaction | varcharNull1UpdatedTransaction | 2019-01-03 01:00:00 |
      | 2  | varcharNotNull2UpdatedTransaction | varcharNull2UpdatedTransaction | 2019-01-03 01:00:00 |
      | 3  | varcharNotNull3UpdatedTransaction | varcharNull3UpdatedTransaction | 2019-01-03 01:00:00 |
    When I commit transaction on connection "light_orm_mysql"
    Then I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository" as active repository
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 2              | 2              |
    And I make repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository" as active repository
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull                 | floatColNotNull | intColNull | varcharColNull                 | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull1UpdatedTransaction | 1.1             | 2          | varcharNull1UpdatedTransaction | 2.2          | 2019-01-01 00:00:00 | 2019-01-03 01:00:00 |
      | 2  | 3             | varcharNotNull2UpdatedTransaction | 3.3             | 4          | varcharNull2UpdatedTransaction | 4.4          | 2019-01-02 00:00:00 | 2019-01-03 01:00:00 |
      | 3  | 5             | varcharNotNull3UpdatedTransaction | 5.5             | 6          | varcharNull3UpdatedTransaction | 6.6          | 2019-01-03 01:00:00 | 2019-01-03 01:00:00 |
