<?php

namespace Oro\Bundle\LayoutBundle\Tests\Unit\Layout\Block\Extension;

use Oro\Component\Layout\Block\Type\BaseType;
use Oro\Component\Layout\BlockInterface;
use Oro\Component\Layout\BlockView;
use Oro\Component\Layout\DataAccessorInterface;
use Oro\Component\Layout\LayoutContext;
use Oro\Component\Layout\OptionValueBag;
use Oro\Bundle\LayoutBundle\Layout\Block\Extension\OptionValueBagExtension;

class OptionValueBagExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionValueBagExtension
     */
    protected $extension;

    protected function setUp()
    {
        $this->extension = new OptionValueBagExtension();
    }

    public function testGetExtendedType()
    {
        $this->assertEquals(BaseType::NAME, $this->extension->getExtendedType());
    }

    /**
     * @param array $actual
     * @param array $expected
     * @param bool $isApplied
     * @dataProvider optionsDataProvider
     */
    public function testNormalizeOptions(array $actual, array $expected, $isApplied = true)
    {
        $context = new LayoutContext();
        $context['expressions_evaluate_deferred'] = !$isApplied;

        /** @var DataAccessorInterface $dataAccessor */
        $dataAccessor = $this->getMock('Oro\Component\Layout\DataAccessorInterface');

        $this->extension->normalizeOptions($actual, $context, $dataAccessor);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array $actual
     * @param array $expected
     * @param bool $isApplied
     * @dataProvider optionsDataProvider
     */
    public function testFinishView(array $actual, array $expected, $isApplied = true)
    {
        $context = new LayoutContext();
        $context['expressions_evaluate_deferred'] = $isApplied;

        $view = new BlockView();
        $view->vars = $actual;

        /** @var BlockInterface|\PHPUnit_Framework_MockObject_MockObject $block */
        $block = $this->getMock('Oro\Component\Layout\BlockInterface');
        $block->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        $this->extension->finishView($view, $block, []);
        $this->assertEquals($expected, $view->vars);
    }

    /**
     * @return array
     */
    public function optionsDataProvider()
    {
        return [
            'not applied' => [
                'actual' => [],
                'expected' => [],
                'isApplied' => false,
            ],
            'empty bag' => [
                'actual' => [
                    'option' => $this->createOptionValueBag([])
                ],
                'expected' => [
                    'option' => '',
                ]
            ],
            'string arguments' => [
                'actual' => [
                    'option' => $this->createOptionValueBag([
                        ['method' => 'add', 'arguments' => ['first']],
                        ['method' => 'add', 'arguments' => ['second']],
                        ['method' => 'replace', 'arguments' => ['first', 'result']],
                        ['method' => 'remove', 'arguments' => ['second']],
                    ])
                ],
                'expected' => [
                    'option' => 'result',
                ]
            ],
            'array arguments' => [
                'actual' => [
                    'option' => $this->createOptionValueBag([
                        ['method' => 'add', 'arguments' => [['one', 'two', 'three']]],
                        ['method' => 'remove', 'arguments' => [['one', 'three']]],
                    ])
                ],
                'expected' => [
                    'option' => ['two']
                ],
            ],
            'recursive processing' => [
                'actual' => [
                    'first_level_option_1' => $this->createOptionValueBag([
                        ['method' => 'add', 'arguments' => ['first_level']],
                    ]),
                    'first_level_option_2' => [
                        'second_level_option_1' => null,
                        'second_level_option_2' => $this->createOptionValueBag([
                            ['method' => 'add', 'arguments' => [['second_level']]],
                        ]),
                    ]
                ],
                'expected' => [
                    'first_level_option_1' => 'first_level',
                    'first_level_option_2' => [
                        'second_level_option_1' => null,
                        'second_level_option_2' => ['second_level']
                    ]
                ]
            ]
        ];
    }

    /**
     * @param array $actions
     * @return OptionValueBag
     */
    protected function createOptionValueBag(array $actions)
    {
        $bag = new OptionValueBag();
        foreach ($actions as $action) {
            call_user_func_array([$bag, $action['method']], $action['arguments']);
        }

        return $bag;
    }
}
