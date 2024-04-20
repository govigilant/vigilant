<?php

namespace Vigilant\Notifications\Tests\Conditions;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Conditions\ConditionEngine;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Tests\Fakes\Conditions\FalseCondition;
use Vigilant\Notifications\Tests\Fakes\Conditions\TrueCondition;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class ConditionEngineTest extends TestCase
{
    #[Test]
    #[DataProvider('conditions')]
    public function it_checks_conditions(array $groups, string $operator, bool $expected): void
    {
        NotificationRegistry::registerCondition(FakeNotification::class, [
            TrueCondition::class,
            FalseCondition::class,
        ]);

        /** @var ConditionEngine $engine */
        $engine = app(ConditionEngine::class);

        $this->assertEquals($expected, $engine->checkGroup(
            FakeNotification::make(1),
            $groups,
            $operator
        ));
    }

    public static function conditions(): array
    {
        return [
            'No conditions' => [
                'groups' => [],
                'operator' => 'all',
                'excpected' => true,
            ],

            'True' => [
                'groups' => [
                    'type' => 'group',
                    'children' => [
                        [
                            'type' => 'condition',
                            'condition' => TrueCondition::class,
                            'operator' => '=',

                        ],
                    ],
                ],
                'operator' => 'all',
                'excpected' => true,
            ],

            'False' => [
                'groups' => [
                    'type' => 'group',
                    'children' => [
                        [
                            'type' => 'condition',
                            'condition' => FalseCondition::class,
                            'operator' => '=',

                        ],
                    ],
                ],
                'operator' => 'all',
                'excpected' => false,
            ],

            'Any' => [
                'groups' => [
                    'type' => 'group',
                    'children' => [
                        [
                            'type' => 'condition',
                            'condition' => FalseCondition::class,
                            'operator' => '=',

                        ],
                        [
                            'type' => 'condition',
                            'condition' => TrueCondition::class,
                            'operator' => '=',

                        ],
                    ],
                ],
                'operator' => 'any',
                'excpected' => true,
            ],

            'All' => [
                'groups' => [
                    'type' => 'group',
                    'children' => [
                        [
                            'type' => 'condition',
                            'condition' => TrueCondition::class,
                            'operator' => '=',

                        ],
                        [
                            'type' => 'condition',
                            'condition' => FalseCondition::class,
                            'operator' => '=',

                        ],
                    ],
                ],
                'operator' => 'all',
                'excpected' => false,
            ],

            'Children' => [
                'groups' => [
                    'type' => 'group',
                    'children' => [
                        [
                            'type' => 'group',
                            'operator' => 'all',
                            'children' => [
                                [
                                    'type' => 'condition',
                                    'condition' => TrueCondition::class,
                                    'operator' => '=',
                                ],
                                [
                                    'type' => 'condition',
                                    'condition' => TrueCondition::class,
                                    'operator' => '=',
                                ],
                            ],
                        ],
                        [
                            'type' => 'group',
                            'operator' => 'all',
                            'children' => [
                                [
                                    'type' => 'condition',
                                    'condition' => TrueCondition::class,
                                    'operator' => '=',
                                ],
                                [
                                    'type' => 'group',
                                    'operator' => 'any',
                                    'children' => [
                                        [
                                            'type' => 'condition',
                                            'condition' => FalseCondition::class,
                                            'operator' => '=',
                                        ],
                                        [
                                            'type' => 'condition',
                                            'condition' => TrueCondition::class,
                                            'operator' => '=',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'operator' => 'all',
                'excpected' => true,
            ],
        ];
    }
}
