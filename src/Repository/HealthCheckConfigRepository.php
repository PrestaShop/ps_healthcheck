<?php

namespace PrestaShop\Module\HealthCheck\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\Module\HealthCheck\Entity\HealthCheckConfig;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Tools;

class HealthCheckConfigRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->tableName = sprintf('%shealth_check_config', $dbPrefix);
    }

    public function getLastHealthCheckConfig(): ?HealthCheckConfig
    {
        /** @var QueryBuilder $qb */
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_health_check_config as id, token, ips')
            ->from($this->tableName)
            ->where(sprintf('id_health_check_config = (SELECT MAX(id_health_check_config) FROM %s)', $this->tableName))
        ;

        $data = $qb->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        if (empty($data[0])) {
            return null;
        }

        return (new HealthCheckConfig())->hydrate($data[0]);
    }

    /**
     * @throws DatabaseException
     */
    public function update(array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->tableName)
            ->set('token', ':token')
            ->set('ips', ':ips')
            ->setParameters([
                'token' => $data['token'],
                'ips' => $data['ips'],
            ])
        ;
        $this->executeQueryBuilder($qb, 'HealthCheck config error');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createTables(): array
    {
        $this->dropTables();

        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s`(
`id_health_check_config` int(10) unsigned NOT NULL auto_increment,
`token` varchar(255) default NULL,
`ips` varchar(255) default NULL,
PRIMARY KEY (`id_health_check_config`)
) ENGINE=%s DEFAULT CHARSET=utf8mb4;',
            $this->tableName,
            _MYSQL_ENGINE_
        );

        $statement = $this->connection->executeQuery($query);
        if (0 != (int) $statement->errorCode()) {
            return [
                'key' => $statement->errorInfo(),
                'parameters' => [],
                'domain' => 'Admin.Modules.Notification',
            ];
        }

        return [];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function installFixtures(): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->tableName)
            ->values([
                'id_health_check_config' => ':idHealthCheckConfig',
                'token' => ':token',
                'ips' => ':ips',
            ])
            ->setParameters([
                'idHealthCheckConfig' => 1,
                'token' => Tools::passwdGen(128, 'RANDOM'),
                'ips' => '127.0.0.1,::1',
            ]);
        $statement = $this->executeQueryBuilder($qb, 'HealthCheck config error');

        if ($statement instanceof Statement && 0 != (int) $statement->errorCode()) {
            return [
                'key' => $statement->errorInfo(),
                'parameters' => [],
                'domain' => 'Admin.Modules.Notification',
            ];
        }

        return [];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dropTables(): array
    {
        $sql = sprintf('DROP TABLE IF EXISTS %s;', $this->tableName);
        $statement = $this->connection->executeQuery($sql);
        if ($statement instanceof Statement && 0 != (int) $statement->errorCode()) {
            return [
                'key' => $statement->errorInfo(),
                'parameters' => [],
                'domain' => 'Admin.Modules.Notification',
            ];
        }

        return [];
    }

    /**
     * @param string $errorPrefix
     *
     * @return Statement|int
     *
     * @throws DatabaseException
     */
    private function executeQueryBuilder(QueryBuilder $qb, $errorPrefix = 'SQL error')
    {
        $statement = $qb->execute();
        if ($statement instanceof Statement && !empty($statement->errorInfo())) {
            throw new DatabaseException($errorPrefix . ': ' . var_export($statement->errorInfo(), true));
        }

        return $statement;
    }
}
