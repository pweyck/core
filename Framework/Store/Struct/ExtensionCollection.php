<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Store\Struct;

use Shopware\Core\Framework\Struct\Collection;

/**
 * @package merchant-services
 *
 * @codeCoverageIgnore
 *
 * @extends Collection<ExtensionStruct>
 */
class ExtensionCollection extends Collection
{
    private int $total = 0;

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function merge(self $collection): self
    {
        foreach ($collection as $entity) {
            if ($this->has($entity->getName())) {
                continue;
            }
            $this->set($entity->getName(), $entity);
        }

        return $this;
    }

    public function filterByType(string $type): self
    {
        return $this->filter(function (ExtensionStruct $ext) use ($type) {
            return $ext->getType() === $type;
        });
    }

    protected function getExpectedClass(): ?string
    {
        return ExtensionStruct::class;
    }
}
