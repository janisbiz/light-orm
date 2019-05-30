Feature: Repository Read Paginator

  Scenario: Reset database
    Given I have existing connection config "light_orm_mysql"
    When I add connection config to connection pool
    Then I reset database for connection "light_orm_mysql"

  Scenario: Prepare data in tables "test_table_one"
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
      | 7  | 13            | varcharNotNull13       | 13.13           | 14         | varcharNull13       | 14.14        | 2019-01-09 00:00:00 |                     |
      | 8  | 15            | varcharNotNull15       | 15.15           | 16         | varcharNull15       | 16.16        | 2019-01-10 00:00:00 |                     |
      | 9  | 17            | varcharNotNull17       | 17.17           | 18         | varcharNull17       | 18.18        | 2019-01-11 00:00:00 |                     |
      | 10 | 19            | varcharNotNull19       | 19.19           | 20         | varcharNull19       | 20.20        | 2019-01-12 00:00:00 |                     |
      | 11 | 21            | varcharNotNull21       | 21.21           | 22         | varcharNull21       | 22.22        | 2019-01-13 00:00:00 |                     |
      | 12 | 23            | varcharNotNull23       | 23.23           | 24         | varcharNull23       | 24.24        | 2019-01-14 00:00:00 |                     |
      | 13 | 25            | varcharNotNull25       | 25.25           | 26         | varcharNull25       | 26.26        | 2019-01-15 00:00:00 |                     |
      | 14 | 27            | varcharNotNull27       | 27.27           | 28         | varcharNull27       | 28.28        | 2019-01-16 00:00:00 |                     |
      | 15 | 29            | varcharNotNull29       | 29.29           | 30         | varcharNull29       | 30.30        | 2019-01-17 00:00:00 |                     |
      | 16 | 31            | varcharNotNull31       | 31.31           | 32         | varcharNull31       | 32.32        | 2019-01-18 00:00:00 |                     |
      | 17 | 33            | varcharNotNull33       | 33.33           | 34         | varcharNull33       | 34.34        | 2019-01-19 00:00:00 |                     |
      | 18 | 35            | varcharNotNull35       | 35.35           | 36         | varcharNull35       | 36.36        | 2019-01-20 00:00:00 |                     |
      | 19 | 37            | varcharNotNull37       | 37.37           | 38         | varcharNull37       | 38.38        | 2019-01-21 00:00:00 |                     |
      | 20 | 39            | varcharNotNull39       | 39.39           | 40         | varcharNull39       | 40.40        | 2019-01-22 00:00:00 |                     |
    Then I commit transaction on connection "light_orm_mysql"

  Scenario: Paginate with one item per page
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createPaginator" on repository which will return paginator with page size of 1 and current page 5
    And I call method "paginate" on paginator which will return entities
    Then I have following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 5  | 9             | varcharNotNull9   | 9.9             | 10         | varcharNull9   | 10.10        | 2019-01-07 00:00:00 |           |
    And I call method "getTotalPages" on paginator which will return following integer 20
    And I call method "getCurrentPageNumber" on paginator which will return following integer 5
    And I call method "getNextPageNumber" on paginator which will return following integer 6
    And I call method "getPreviousPageNumber" on paginator which will return following integer 4
    And I call method "getResultTotalCount" on paginator which will return following integer 20
    And I call method "getPageSize" on paginator which will return following integer 1
    And I get following page numbers from paginator:
      | Number |
      | 3      |
      | 4      |
      | 5      |
      | 6      |
      | 7      |

  Scenario: Paginate with more than one item per page
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createPaginator" on repository which will return paginator with page size of 5 and current page 3
    And I call method "paginate" on paginator which will return entities
    Then I have following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 11 | 21            | varcharNotNull21  | 21.21           | 22         | varcharNull21  | 22.22        | 2019-01-13 00:00:00 |           |
      | 12 | 23            | varcharNotNull23  | 23.23           | 24         | varcharNull23  | 24.24        | 2019-01-14 00:00:00 |           |
      | 13 | 25            | varcharNotNull25  | 25.25           | 26         | varcharNull25  | 26.26        | 2019-01-15 00:00:00 |           |
      | 14 | 27            | varcharNotNull27  | 27.27           | 28         | varcharNull27  | 28.28        | 2019-01-16 00:00:00 |           |
      | 15 | 29            | varcharNotNull29  | 29.29           | 30         | varcharNull29  | 30.30        | 2019-01-17 00:00:00 |           |
    And I call method "getTotalPages" on paginator which will return following integer 4
    And I call method "getCurrentPageNumber" on paginator which will return following integer 3
    And I call method "getNextPageNumber" on paginator which will return following integer 4
    And I call method "getPreviousPageNumber" on paginator which will return following integer 2
    And I call method "getResultTotalCount" on paginator which will return following integer 20
    And I call method "getPageSize" on paginator which will return following integer 5
    And I get following page numbers from paginator:
      | Number |
      | 1      |
      | 2      |
      | 3      |
      | 4      |

  Scenario: Paginate Fake with one item per page
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createPaginator" on repository which will return paginator with page size of 1 and current page 10
    And I call method "paginateFake" on paginator which will return entities
    Then I have following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 10 | 19            | varcharNotNull19  | 19.19           | 20         | varcharNull19  | 20.20        | 2019-01-12 00:00:00 |           |
    And I call method "getTotalPages" on paginator which will return following integer 11
    And I call method "getCurrentPageNumber" on paginator which will return following integer 10
    And I call method "getNextPageNumber" on paginator which will return following integer 11
    And I call method "getPreviousPageNumber" on paginator which will return following integer 9
    And I call method "getPageSize" on paginator which will return following integer 1
    And I get following page numbers from paginator:
      | Number |
      | 8      |
      | 9      |
      | 10     |
      | 11     |

  Scenario: Paginate Fake with more than one item per page
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createPaginator" on repository which will return paginator with page size of 5 and current page 2
    And I call method "paginateFake" on paginator which will return entities
    Then I have following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
      | 6  | 11            | varcharNotNull11  | 11.11           | 12         | varcharNull11  | 12.12        | 2019-01-08 00:00:00 |           |
      | 7  | 13            | varcharNotNull13  | 13.13           | 14         | varcharNull13  | 14.14        | 2019-01-09 00:00:00 |           |
      | 8  | 15            | varcharNotNull15  | 15.15           | 16         | varcharNull15  | 16.16        | 2019-01-10 00:00:00 |           |
      | 9  | 17            | varcharNotNull17  | 17.17           | 18         | varcharNull17  | 18.18        | 2019-01-11 00:00:00 |           |
      | 10 | 19            | varcharNotNull19  | 19.19           | 20         | varcharNull19  | 20.20        | 2019-01-12 00:00:00 |           |
    And I call method "getTotalPages" on paginator which will return following integer 3
    And I call method "getCurrentPageNumber" on paginator which will return following integer 2
    And I call method "getNextPageNumber" on paginator which will return following integer 3
    And I call method "getPreviousPageNumber" on paginator which will return following integer 1
    And I call method "getPageSize" on paginator which will return following integer 5
    And I get following page numbers from paginator:
      | Number |
      | 1      |
      | 2      |
      | 3      |

  Scenario: Paginate Fake without result
    Given I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create repository "Janisbiz\LightOrm\Tests\Behat\Bootstrap\Generated\LightOrmMysql\Repository\TestTableOneRepository"
    When I call method "createPaginator" on repository which will return paginator with page size of 5 and current page 5
    And I call method "paginateFake" on paginator which will return entities
    Then I have following rows:
      | id | intColNotNull | varcharColNotNull | floatColNotNull | intColNull | varcharColNull | floatColNull | createdAt           | updatedAt |
    And I call method "getTotalPages" on paginator which will return following integer 5
    And I call method "getCurrentPageNumber" on paginator which will return following integer 5
    And I call method "getNextPageNumber" on paginator which will return following integer 6
    And I call method "getPreviousPageNumber" on paginator which will return following integer 4
    And I call method "getPageSize" on paginator which will return following integer 5
    And I get following page numbers from paginator:
      | Number |
      | 2      |
      | 3      |
      | 4      |
