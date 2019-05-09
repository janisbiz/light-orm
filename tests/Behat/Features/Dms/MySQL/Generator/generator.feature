Feature: Generator

  Scenario: Generate
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create generator for connection "light_orm_mysql"
    And I add writers to generator
    When I run generator
    Then Then I have following files generated:
      | var/light-orm/Generated/LightOrmMysql/Base/BaseTestTableOne.php                        |
      | var/light-orm/Generated/LightOrmMysql/Base/BaseTestTableOneTwo.php                     |
      | var/light-orm/Generated/LightOrmMysql/Base/BaseTestTableTwo.php                        |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Entity/TestTableOneEntity.php            |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Entity/TestTableOneTwoEntity.php         |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Entity/TestTableTwoEntity.php            |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Repository/TestTableOneRepository.php    |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Repository/TestTableOneTwoRepository.php |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Repository/TestTableTwoRepository.php    |
      | tests/Behat/Bootstrap/Generated/LightOrmMysql/Repository/TestTableTwoRepository.php    |

  Scenario: Generate With Directory Override (All files are new)
    When I have existing connection config "light_orm_mysql"
    And I add connection config to connection pool
    And I create generator for connection "light_orm_mysql"
    And I add writers to generator with directory override "var/behat/generated"
    When I run generator
    Then Then I have following files generated:
      | var/behat/generated/LightOrmMysql/Base/BaseTestTableOne.php                |
      | var/behat/generated/LightOrmMysql/Base/BaseTestTableOneTwo.php             |
      | var/behat/generated/LightOrmMysql/Base/BaseTestTableTwo.php                |
      | var/behat/generated/LightOrmMysql/Entity/TestTableOneEntity.php            |
      | var/behat/generated/LightOrmMysql/Entity/TestTableOneTwoEntity.php         |
      | var/behat/generated/LightOrmMysql/Entity/TestTableTwoEntity.php            |
      | var/behat/generated/LightOrmMysql/Repository/TestTableOneRepository.php    |
      | var/behat/generated/LightOrmMysql/Repository/TestTableOneTwoRepository.php |
      | var/behat/generated/LightOrmMysql/Repository/TestTableTwoRepository.php    |
      | var/behat/generated/LightOrmMysql/Repository/TestTableTwoRepository.php    |
