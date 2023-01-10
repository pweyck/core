<?php declare(strict_types=1);

namespace Shopware\Core\Content\Flow\Rule;

use Shopware\Core\Framework\Rule\FlowRule;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;

class OrderDeliveryStatusRule extends FlowRule
{
    public const RULE_NAME = 'orderDeliveryStatus';

    /**
     * @internal
     */
    public function __construct(public string $operator = Rule::OPERATOR_EQ, public ?array $stateIds = null)
    {
        parent::__construct();
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::uuidOperators(false),
            'stateName' => RuleConstraints::uuids(),
        ];
    }

    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof FlowRuleScope) {
            return false;
        }

        if (!$deliveries = $scope->getOrder()->getDeliveries()) {
            return false;
        }

        $deliveryStateIds = [];
        foreach ($deliveries as $delivery) {
            $deliveryStateIds[] = $delivery->getStateId();
        }

        return RuleComparison::uuids($deliveryStateIds, $this->stateIds, $this->operator);
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->entitySelectField('stateIds',StateMachineStateDefinition::ENTITY_NAME,true);
    }
}
