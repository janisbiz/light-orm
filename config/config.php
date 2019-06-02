<?php

\defined('JANISBIZ_LIGHT_ORM_ROOT_DIR') || \define(
    'JANISBIZ_LIGHT_ORM_ROOT_DIR',
    \implode(
        '',
        [
            __DIR__,
            DIRECTORY_SEPARATOR,
            '..',
            DIRECTORY_SEPARATOR,
        ]
    )
);
\defined('JANISBIZ_LIGHT_ORM_BEHAT_CONFIG_DIR') || \define(
    'JANISBIZ_LIGHT_ORM_BEHAT_CONFIG_DIR',
    \implode(
        '',
        [
            __DIR__,
            DIRECTORY_SEPARATOR,
            'tests',
            DIRECTORY_SEPARATOR,
            'behat',
            DIRECTORY_SEPARATOR
        ]
    )
);
