<?php
declare(strict_types = 1);

namespace Properties\Formal\AccessLayer;

use Formal\AccessLayer\{
    Connection as Concrete,
    Query\CreateTable,
    Query\DropTable,
    Table\Name,
    Table\Column,
};
use Innmind\BlackBox\{
    Set,
    Property,
};

final class Connection
{
    /**
     * @return Set<Property>
     */
    public static function any(): Set
    {
        return Set\Properties::any(...\array_map(
            static fn($class) => [$class, 'any'](),
            self::list(),
        ));
    }

    /**
     * @return list<class-string<Property>>
     */
    public static function list(): array
    {
        return [
            Connection\AllowToStartTwoQueriesInParallel::class,
            Connection\AnInvalidQueryMustThrow::class,
            Connection\AnInvalidLazyQueryMustThrow::class,
            Connection\AnInvalidLazySelectMustThrow::class,
            Connection\AQueryWithoutTheCorrectNumberOfParametersMustThrow::class,
            Connection\MustThrowWhenValueDoesntFitTheSchema::class,
            Connection\Insert::class,
            Connection\MultipleInsertsAtOnce::class,
            Connection\ParametersCanBeBoundByName::class,
            Connection\ParametersCanBeBoundByIndex::class,
            Connection\ContentInsertedAfterStartOfTransactionIsAccessible::class,
            Connection\ContentIsAccessibleAfterCommit::class,
            Connection\ContentIsNotAccessibleAfterRollback::class,
            Connection\CommittingAnUnstartedTransactionMustThrow::class,
            Connection\RollbackingAnUnstartedTransactionMustThrow::class,
            Connection\ParameterTypesCanBeSpecified::class,
            Connection\CreateTable::class,
            Connection\CreateTableWithPrimaryKey::class,
            Connection\CreateTableWithForeignKey::class,
            Connection\CreatingSameTableTwiceMustThrow::class,
            Connection\CreateTableIfNotExists::class,
            Connection\CanDropUnknownDatabaseIfNotExists::class,
            Connection\DroppingUnknownDatabaseMustThrow::class,
            Connection\SelectEverything::class,
            Connection\SelectCount::class,
            Connection\SelectColumns::class,
            Connection\SelectAliasedColumns::class,
            Connection\SelectWhere::class,
            Connection\SelectWhereStartsWith::class,
            Connection\SelectWhereEndsWith::class,
            Connection\SelectWhereContains::class,
            Connection\SelectWhereIn::class,
            Connection\SelectWhereInQuery::class,
            Connection\SelectOffset::class,
            Connection\SelectLimit::class,
            Connection\SelectOrder::class,
            Connection\Update::class,
            Connection\UpdateSpecificRow::class,
            Connection\Delete::class,
            Connection\DeleteSpecificRow::class,
        ];
    }

    public static function seed(Concrete $connection): void
    {
        $connection(DropTable::ifExists(new Name('test')));
        $connection(DropTable::ifExists(new Name('test_values')));
        $connection(CreateTable::named(
            new Name('test'),
            new Column(new Column\Name('id'), Column\Type::char(36)),
            new Column(new Column\Name('username'), Column\Type::varchar(255)),
            new Column(new Column\Name('registerNumber'), Column\Type::bigint()),
        )->primaryKey(new Column\Name('id')));
        $connection(CreateTable::named(
            new Name('test_values'),
            new Column(new Column\Name('id'), Column\Type::char(36)),
            new Column(new Column\Name('value'), Column\Type::varchar(255)),
        ));
    }
}
