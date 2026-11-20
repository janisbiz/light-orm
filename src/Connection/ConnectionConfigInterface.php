<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Connection;

interface ConnectionConfigInterface
{
    public function generateDsn(): string;

    public function getUsername(): string;

    public function getPassword(): string;

    public function getDbname(): string;

    public function getAdapterConnectionClass(): string;
}
