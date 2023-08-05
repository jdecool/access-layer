<?php
declare(strict_types = 1);

namespace Properties\Formal\AccessLayer\Connection;

use Formal\AccessLayer\{
    Query\SQL,
    Query\Parameter,
    Query\Select,
    Table\Name,
    Connection,
};
use Innmind\BlackBox\{
    Property,
    Set,
    Runner\Assert,
};

/**
 * @implements Property<Connection>
 */
final class SelectOffset implements Property
{
    private string $uuid;
    private string $username;
    private int $number;
    private int $offset;

    public function __construct(
        string $uuid,
        string $username,
        int $number,
        int $offset,
    ) {
        $this->uuid = $uuid;
        $this->username = $username;
        $this->number = $number;
        $this->offset = $offset;
    }

    public static function any(): Set
    {
        return Set\Composite::immutable(
            static fn(...$args) => new self(...$args),
            Set\Uuid::any(),
            Set\Strings::madeOf(Set\Chars::ascii())->between(0, 255),
            Set\Integers::any(),
            Set\Integers::above(1),
        );
    }

    public function applicableTo(object $connection): bool
    {
        return true;
    }

    public function ensureHeldBy(Assert $assert, object $connection): object
    {
        $insert = SQL::of('INSERT INTO `test` VALUES (?, ?, ?);')
            ->with(Parameter::of($this->uuid))
            ->with(Parameter::of($this->username))
            ->with(Parameter::of($this->number));
        $connection($insert);

        $count = $connection(Select::from(Name::of('test')))->size();

        $rows = $connection(
            Select::from(Name::of('test'))->limit(\PHP_INT_MAX, $this->offset),
        );

        $assert
            ->number($rows->size())
            ->lessThanOrEqual(\max(0, $count - $this->offset));

        return $connection;
    }
}
