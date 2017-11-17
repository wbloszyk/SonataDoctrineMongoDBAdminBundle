<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineMongoDBAdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\CoreBundle\Form\Type\BooleanType;

class BooleanFilter extends Filter
{
    /**
     * @param ProxyQueryInterface $queryBuilder
     * @param string              $alias
     * @param string              $field
     * @param mixed               $data
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('type', $data) || !array_key_exists('value', $data)) {
            return;
        }

        if (is_array($data['value'])) {
            $values = [];
            foreach ($data['value'] as $v) {
                if (!in_array($v, [BooleanType::TYPE_NO, BooleanType::TYPE_YES])) {
                    continue;
                }

                $values[] = (BooleanType::TYPE_YES == $v) ? true : false;
            }

            if (0 == count($values)) {
                return;
            }

            $queryBuilder->field($field)->in($values);
            $this->active = true;
        } else {
            if (!in_array($data['value'], [BooleanType::TYPE_NO, BooleanType::TYPE_YES])) {
                return;
            }

            $value = BooleanType::TYPE_YES == $data['value'] ? true : false;

            $queryBuilder->field($field)->equals($value);
            $this->active = true;
        }
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return [];
    }

    public function getRenderSettings()
    {
        return ['sonata_type_filter_default', [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'operator_type' => 'hidden',
            'operator_options' => [],
            'label' => $this->getLabel(),
        ]];
    }
}