<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2020/03/31
 * Time: 21:00
 */

namespace Tests\KitLoong\MigrationsGenerator\Generators;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use KitLoong\MigrationsGenerator\Generators\DatetimeField;
use KitLoong\MigrationsGenerator\Generators\DecimalField;
use KitLoong\MigrationsGenerator\Generators\EnumField;
use KitLoong\MigrationsGenerator\Generators\FieldGenerator;
use KitLoong\MigrationsGenerator\Generators\IntegerField;
use KitLoong\MigrationsGenerator\Generators\Modifier\CommentModifier;
use KitLoong\MigrationsGenerator\Generators\Modifier\DefaultModifier;
use KitLoong\MigrationsGenerator\Generators\Modifier\IndexModifier;
use KitLoong\MigrationsGenerator\Generators\Modifier\NullableModifier;
use KitLoong\MigrationsGenerator\Generators\OtherField;
use KitLoong\MigrationsGenerator\Generators\SetField;
use KitLoong\MigrationsGenerator\Generators\StringField;
use KitLoong\MigrationsGenerator\MigrationMethod\ColumnModifier;
use KitLoong\MigrationsGenerator\MigrationMethod\ColumnType;
use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;

class FieldGeneratorTest extends TestCase
{
    public function testFieldTypeMap()
    {
        /** @var FieldGenerator $fieldGenerator */
        $fieldGenerator = resolve(FieldGenerator::class);

        $this->assertSame([
            'smallint' => ColumnType::SMALL_INTEGER,
            'mediumint' => ColumnType::MEDIUM_INTEGER,
            'bigint' => ColumnType::BIG_INTEGER,
            'datetime' => ColumnType::DATETIME,
            'blob' => ColumnType::BINARY
        ], $fieldGenerator::$fieldTypeMap);
    }

    public function testGenerateEmptyColumn()
    {
        /** @var FieldGenerator $fieldGenerator */
        $fieldGenerator = resolve(FieldGenerator::class);

        $table = Mockery::mock(Table::class);
        $index = ['index'];
        $indexes = collect([$index]);

        $table->shouldReceive('getColumns')
            ->andReturn([]);

        $fields = $fieldGenerator->generate($table, $indexes);
        $this->assertEmpty($fields);
    }

    public function testGenerateEmptyField()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            Types::INTEGER,
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(IntegerField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column, $indexes)
                    ->andReturn([]);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertEmpty($fields);
        }
    }

    public function testGenerateInteger()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            Types::SMALLINT,
            Types::INTEGER,
            Types::BIGINT,
            'mediumint'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(IntegerField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column, $indexes)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateDatetime()
    {
        $types = [
            Types::DATETIME_MUTABLE,
            'timestamp'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(DatetimeField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $mock->shouldReceive('isUseTimestamps')
                    ->andReturnFalse();

                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column, false)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateDecimal()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            Types::DECIMAL,
            Types::FLOAT,
            'double'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(DecimalField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateEnum()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            'enum'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(EnumField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with('table', $field)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateSet()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            'set'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(SetField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with('table', $field)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateString()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            Types::STRING
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(StringField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateOtherType()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            'geometry'
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect([$index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnTrue();
            $column->shouldReceive('getDefault')
                ->andReturnNull();
            $column->shouldReceive('getComment')
                ->andReturnNull();
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(OtherField::class, function (MockInterface $mock) use ($field) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field)
                    ->andReturn($returnField);
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame('returned', $fields[0]['field']);
        }
    }

    public function testGenerateWithAllModifier()
    {
        $this->mock(DatetimeField::class, function (MockInterface $mock) {
            $mock->shouldReceive('isUseTimestamps')
                ->andReturnFalse();
        });

        $types = [
            Types::INTEGER,
        ];

        foreach ($types as $type) {
            $table = Mockery::mock(Table::class);
            $index = ['index'];
            $column = Mockery::mock(Column::class);
            $indexes = collect(['returned' => $index]);

            $table->shouldReceive('getName')
                ->andReturn('table');
            $table->shouldReceive('getColumns')
                ->andReturn([$column]);

            $column->shouldReceive('getName')
                ->andReturn('name');
            $column->shouldReceive('getNotnull')
                ->andReturnFalse();
            $column->shouldReceive('getDefault')
                ->andReturn('default');
            $column->shouldReceive('getComment')
                ->andReturn('comment');
            $column->shouldReceive('getType->getName')
                ->andReturn($type);

            $field = [
                'field' => 'name',
                'type' => $type,
                'args' => [],
                'decorators' => []
            ];

            $this->mock(IntegerField::class, function (MockInterface $mock) use ($field, $column, $indexes) {
                $returnField = $field;
                $returnField['field'] = 'returned';
                $mock->shouldReceive('makeField')
                    ->with($field, $column, $indexes)
                    ->andReturn($returnField);
            });

            $this->mock(NullableModifier::class, function (MockInterface $mock) {
                $mock->shouldReceive('shouldAddNullableModifier')
                    ->with(ColumnType::INTEGER)
                    ->andReturnTrue();
            });

            $this->mock(DefaultModifier::class, function (MockInterface $mock) use ($column) {
                $mock->shouldReceive('generate')
                    ->with(Types::INTEGER, $column)
                    ->andReturn('default(default)');
            });

            $this->mock(IndexModifier::class, function (MockInterface $mock) use ($index) {
                $mock->shouldReceive('generate')
                    ->with($index)
                    ->andReturn('index');
            });

            $this->mock(CommentModifier::class, function (MockInterface $mock) use ($column) {
                $mock->shouldReceive('generate')
                    ->with('comment')
                    ->andReturn("comment('comment')");
            });

            /** @var FieldGenerator $fieldGenerator */
            $fieldGenerator = resolve(FieldGenerator::class);

            $fields = $fieldGenerator->generate($table, $indexes);
            $this->assertSame([
                [
                    'field' => 'returned',
                    'type' => ColumnType::INTEGER,
                    'args' => [],
                    'decorators' => [
                        ColumnModifier::NULLABLE,
                        'default(default)',
                        'index',
                        "comment('comment')"
                    ]
                ]
            ], $fields);
        }
    }
}
