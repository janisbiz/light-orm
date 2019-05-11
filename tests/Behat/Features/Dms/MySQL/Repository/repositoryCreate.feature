Feature: Repository Create

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I reset database for connection "light_orm_mysql"

  Scenario: Create rows in table "test_table_one"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |           |

  Scenario: Create rows in table "test_table_one" with exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "create" on repository with parameters and expecting exception:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 |           |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'PRIMARY'"

  Scenario: Create rows in table "test_table_one" with duplicate key update
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createOnDuplicateKeyUpdate" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 01:00:00 |
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 01:00:00 |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |                     |

  Scenario: Create rows in table "test_table_one" with ignore
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createIgnore" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 15:00:00 |
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull    | 1.1             | 2          | varcharNull    | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 01:00:00 |
      | 2  | 3             | varcharNotNull2   | 3.3             | 4          | varcharNull2   | 4.4          | 2019-01-02 00:00:00 |                     |

  Scenario: Create rows in table "test_table_one" with replace
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createReplace" on repository with parameters:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
    Then I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |

  Scenario: Create rows in table "test_table_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    When I call method "create" on repository with parameters:
      | id |
      | 1  |
      | 2  |
    Then I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |

  Scenario: Create rows in table "test_table_two" with exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    When I call method "create" on repository with parameters and expecting exception:
      | id |
      | 1  |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'PRIMARY'"

  Scenario: Create rows in table "test_table_one_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    When I call method "create" on repository with parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
    Then I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |

  Scenario: Create rows in table "test_table_one_two" with exception
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    When I call method "create" on repository with parameters and expecting exception:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
    Then I have exception with message "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1-2' for key 'PRIMARY'"

  Scenario: Create rows in tables "test_table_one", "test_table_one_two", "test_table_one_two" when in transaction with rollback
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 3  | 5             | varcharNotNull3   | 5.5             | 6          | varcharNull3   | 6.6          | 2019-01-04 00:00:00 |           |
      | 4  | 7             | varcharNotNull4   | 7.7             | 8          | varcharNull4   | 8.8          | 2019-01-04 00:00:00 |           |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      | 3  |
      | 4  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "create" on repository with parameters:
      | testTableOneId | testTableTwoId |
      | 3              | 4              |
      | 4              | 3              |
    And I rollback transaction on connection "light_orm_mysql"
    Then I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |

  Scenario: Create rows in tables "test_table_one", "test_table_one_two", "test_table_one_two" when in transaction
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 3  | 5             | varcharNotNull3   | 5.5             | 6          | varcharNull3   | 6.6          | 2019-01-04 00:00:00 |           |
      | 4  | 7             | varcharNotNull4   | 7.7             | 8          | varcharNull4   | 8.8          | 2019-01-04 00:00:00 |           |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      | 3  |
      | 4  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "create" on repository with parameters:
      | testTableOneId | testTableTwoId |
      | 3              | 4              |
      | 4              | 3              |
    And I commit transaction on connection "light_orm_mysql"
    Then I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3       | 5.5             | 6          | varcharNull3       | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4       | 7.7             | 8          | varcharNull4       | 8.8          | 2019-01-04 00:00:00 |                     |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 2              |
      | 2              | 1              |
      | 3              | 4              |
      | 4              | 3              |

  Scenario: Create rows in table "test_table_one" with auto increment IDs
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      |    | 9             | varcharNotNull9   | 9.9             | 10         | varcharNull9   | 10.10        | 2019-01-07 00:00:00 |           |
      |    | 11            | varcharNotNull11  | 11.11           | 12         | varcharNull11  | 12.12        | 2019-01-08 00:00:00 |           |
    Then I have following entities with identifiers set:
      | id |
      | 5  |
      | 6  |
    And I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3       | 5.5             | 6          | varcharNull3       | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4       | 7.7             | 8          | varcharNull4       | 8.8          | 2019-01-04 00:00:00 |                     |
      | 5  | 9             | varcharNotNull9       | 9.9             | 10         | varcharNull9       | 10.10        | 2019-01-07 00:00:00 |                     |
      | 6  | 11            | varcharNotNull11      | 11.11           | 12         | varcharNull11      | 12.12        | 2019-01-08 00:00:00 |                     |

  Scenario: Create rows in table "test_table_two" with auto increment IDs
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    When I call method "create" on repository with parameters:
      | id |
      |    |
      |    |
    Then I have following entities with identifiers set:
      | id |
      | 5  |
      | 6  |
    And I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
      | 5  |
      | 6  |

  Scenario: Create rows in tables "test_table_one", "test_table_one_two" with auto increment when in transaction with rollback
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      |    | 13            | varcharNotNull3   | 13.13           | 14         | varcharNull3   | 14.14        | 2019-01-04 00:00:00 |           |
      |    | 15            | varcharNotNull4   | 15.15           | 16         | varcharNull4   | 16.16        | 2019-01-04 00:00:00 |           |
    Then I have following entities with identifiers set:
      | id |
      | 7  |
      | 8  |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      |    |
      |    |
    Then I have following entities with identifiers set:
      | id |
      | 7  |
      | 8  |
    And I rollback transaction on connection "light_orm_mysql"
    Then I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3       | 5.5             | 6          | varcharNull3       | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4       | 7.7             | 8          | varcharNull4       | 8.8          | 2019-01-04 00:00:00 |                     |
      | 5  | 9             | varcharNotNull9       | 9.9             | 10         | varcharNull9       | 10.10        | 2019-01-07 00:00:00 |                     |
      | 6  | 11            | varcharNotNull11      | 11.11           | 12         | varcharNull11      | 12.12        | 2019-01-08 00:00:00 |                     |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
      | 5  |
      | 6  |

  Scenario: Create rows in tables "test_table_one", "test_table_one_two" with auto increment when in transaction
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      |    | 13            | varcharNotNull3   | 13.13           | 14         | varcharNull3   | 14.14        | 2019-01-04 00:00:00 |           |
      |    | 15            | varcharNotNull4   | 15.15           | 16         | varcharNull4   | 16.16        | 2019-01-04 00:00:00 |           |
    Then I have following entities with identifiers set:
      | id |
      | 9  |
      | 10 |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      |    |
      |    |
    Then I have following entities with identifiers set:
      | id |
      | 9  |
      | 10 |
    And I commit transaction on connection "light_orm_mysql"
    Then I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull     | floatColNotNull | intColNull | varcharColNull     | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNullReplace | 1.1             | 2          | varcharNullReplace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2       | 3.3             | 4          | varcharNull2       | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3       | 5.5             | 6          | varcharNull3       | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4       | 7.7             | 8          | varcharNull4       | 8.8          | 2019-01-04 00:00:00 |                     |
      | 5  | 9             | varcharNotNull9       | 9.9             | 10         | varcharNull9       | 10.10        | 2019-01-07 00:00:00 |                     |
      | 6  | 11            | varcharNotNull11      | 11.11           | 12         | varcharNull11      | 12.12        | 2019-01-08 00:00:00 |                     |
      | 9  | 13            | varcharNotNull3       | 13.13           | 14         | varcharNull3       | 14.14        | 2019-01-04 00:00:00 |                     |
      | 10 | 15            | varcharNotNull4       | 15.15           | 16         | varcharNull4       | 16.16        | 2019-01-04 00:00:00 |                     |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
      | 5  |
      | 6  |
      | 9  |
      | 10 |
