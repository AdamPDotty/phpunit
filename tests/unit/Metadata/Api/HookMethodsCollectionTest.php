<?php

declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace unit\Metadata\Api;

use function iterator_to_array;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Api\HookMethod;
use PHPUnit\Metadata\Api\HookMethodCollection;

#[CoversClass(HookMethodCollection::class)]
#[Small]
#[Group('metadata')]
final class HookMethodsCollectionTest extends TestCase
{
    public static function provider(): iterable
    {
        return [
            [
                HookMethodCollection::defaultBeforeClass()->add(new HookMethod('someMethod', 0)),
                ['someMethod', 'setUpBeforeClass'],
            ],
            [
                HookMethodCollection::defaultBefore()->add(new HookMethod('someMethod', 0)),
                ['someMethod', 'setUp'],
            ],
            [
                HookMethodCollection::defaultPreCondition()->add(new HookMethod('someMethod', 0)),
                ['someMethod', 'assertPreConditions'],
            ],
            [
                HookMethodCollection::defaultPostCondition()->add(new HookMethod('someMethod', 0)),
                ['assertPostConditions', 'someMethod'],
            ],
            [
                HookMethodCollection::defaultAfter()->add(new HookMethod('someMethod', 0)),
                ['tearDown', 'someMethod'],
            ],
            [
                HookMethodCollection::defaultAfterClass()->add(new HookMethod('someMethod', 0)),
                ['tearDownAfterClass', 'someMethod'],
            ],
            [
                HookMethodCollection::defaultBeforeClass()
                    ->add(new HookMethod('methodWithHighPriority', priority: 1))
                    ->add(new HookMethod('methodWithVeryLowPriority', priority: -10))
                    ->add(new HookMethod('methodWithLowPriority', priority: -1))
                    ->add(new HookMethod('methodWithVeryHighPriority', priority: 10))
                    ->add(new HookMethod('methodWithoutPriority', 0)),
                [
                    'methodWithVeryHighPriority',
                    'methodWithHighPriority',
                    'methodWithoutPriority',
                    'setUpBeforeClass',
                    'methodWithLowPriority',
                    'methodWithVeryLowPriority',
                ],
            ],
        ];
    }

    #[DataProvider('provider')]
    public function testIterator(HookMethodCollection $hookMethodsCollection, array $expected): void
    {
        $this->assertSame($expected, iterator_to_array($hookMethodsCollection));
    }
}
