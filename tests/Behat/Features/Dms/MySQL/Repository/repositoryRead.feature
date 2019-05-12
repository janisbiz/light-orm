Feature: Repository Read

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I reset database for connection "light_orm_mysql"

  Scenario: Prepare data in tables "test_table_one", "test_table_two", "test_table_one_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I begin transaction on connection "light_orm_mysql"
    When I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    And I call method "create" on repository with parameters:
      | id | intColNotNull | varcharColNotNull      | floatColNotNull | intColNull | varcharColNull      | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull1Replace | 1.1             | 2          | varcharNull1Replace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2        | 3.3             | 4          | varcharNull2        | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3        | 5.5             | 6          | varcharNull3        | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4        | 7.7             | 8          | varcharNull4        | 8.8          | 2019-01-04 00:00:00 |                     |
      | 5  | 9             | varcharNotNull9        | 9.9             | 10         | varcharNull9        | 10.10        | 2019-01-07 00:00:00 |                     |
      | 6  | 11            | varcharNotNull11       | 11.11           | 12         | varcharNull11       | 12.12        | 2019-01-08 00:00:00 |                     |
      | 7  | 13            | varcharNotNull11       | 13.13           | 14         | varcharNull11       | 14.14        | 2019-01-08 00:00:00 |                     |
      | 8  | 15            | varcharNotNull11       | 15.15           | 16         | varcharNull11       | 16.16        | 2019-01-08 00:00:00 |                     |
      | 9  | 17            | varcharNotNull3        | 17.17           | 18         | varcharNull3        | 18.18        | 2019-01-04 00:00:00 |                     |
      | 10 | 19            | varcharNotNull4        | 19.19           | 20         | varcharNull4        | 20.20        | 2019-01-04 00:00:00 |                     |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    And I call method "create" on repository with parameters:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
      | 5  |
      | 6  |
      | 7  |
      | 8  |
      | 9  |
      | 10 |
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    And I call method "create" on repository with parameters:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 1              | 10             |
      | 2              | 2              |
      | 2              | 9              |
      | 3              | 3              |
      | 3              | 8              |
      | 4              | 4              |
      | 4              | 7              |
      | 5              | 5              |
      | 5              | 6              |
      | 6              | 5              |
      | 6              | 6              |
      | 7              | 4              |
      | 7              | 7              |
      | 8              | 3              |
      | 8              | 8              |
      | 9              | 2              |
      | 9              | 9              |
      | 10             | 1              |
      | 10             | 10             |
    Then I commit transaction on connection "light_orm_mysql"

  Scenario: Read rows from table "test_table_one"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "read" on repository which will return following rows:
      | id | intColNotNull | varcharColNotNull      | floatColNotNull | intColNull | varcharColNull      | floatColNull | createdAt           | updatedAt           |
      | 1  | 1             | varcharNotNull1Replace | 1.1             | 2          | varcharNull1Replace | 2.2          | 2019-01-01 00:00:00 | 2019-01-01 02:00:00 |
      | 2  | 3             | varcharNotNull2        | 3.3             | 4          | varcharNull2        | 4.4          | 2019-01-02 00:00:00 |                     |
      | 3  | 5             | varcharNotNull3        | 5.5             | 6          | varcharNull3        | 6.6          | 2019-01-04 00:00:00 |                     |
      | 4  | 7             | varcharNotNull4        | 7.7             | 8          | varcharNull4        | 8.8          | 2019-01-04 00:00:00 |                     |
      | 5  | 9             | varcharNotNull9        | 9.9             | 10         | varcharNull9        | 10.10        | 2019-01-07 00:00:00 |                     |
      | 6  | 11            | varcharNotNull11       | 11.11           | 12         | varcharNull11       | 12.12        | 2019-01-08 00:00:00 |                     |
      | 7  | 13            | varcharNotNull11       | 13.13           | 14         | varcharNull11       | 14.14        | 2019-01-08 00:00:00 |                     |
      | 8  | 15            | varcharNotNull11       | 15.15           | 16         | varcharNull11       | 16.16        | 2019-01-08 00:00:00 |                     |
      | 9  | 17            | varcharNotNull3        | 17.17           | 18         | varcharNull3        | 18.18        | 2019-01-04 00:00:00 |                     |
      | 10 | 19            | varcharNotNull4        | 19.19           | 20         | varcharNull4        | 20.20        | 2019-01-04 00:00:00 |                     |

  Scenario: Read rows from table "test_table_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableTwoRepository"
    When I call method "read" on repository which will return following rows:
      | id |
      | 1  |
      | 2  |
      | 3  |
      | 4  |
      | 5  |
      | 6  |
      | 7  |
      | 8  |
      | 9  |
      | 10 |

  Scenario: Read rows from table "test_table_one_two"
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneTwoRepository"
    When I call method "read" on repository which will return following rows:
      | testTableOneId | testTableTwoId |
      | 1              | 1              |
      | 1              | 10             |
      | 2              | 2              |
      | 2              | 9              |
      | 3              | 3              |
      | 3              | 8              |
      | 4              | 4              |
      | 4              | 7              |
      | 5              | 5              |
      | 5              | 6              |
      | 6              | 5              |
      | 6              | 6              |
      | 7              | 4              |
      | 7              | 7              |
      | 8              | 3              |
      | 8              | 8              |
      | 9              | 2              |
      | 9              | 9              |
      | 10             | 1              |
      | 10             | 10             |
