<?php

namespace Akeneo\Pim\tests\Api\AttributeGroup;

use Akeneo\Pim\tests\Api\ApiTestCase;

class GetAttributeGroupIntegration extends ApiTestCase
{
    public function testGet()
    {
        $api = $this->createClient()->getAttributeGroupApi();

        $attributeGroup = $api->get('info');

        $this->assertSameContent([
            'code'       => 'info',
            'attributes' => ['sku', 'name', 'manufacturer', 'weather_conditions', 'description', 'length'],
            'sort_order' => 1,
            'labels'     => [
                'en_US' => 'Product information',
            ],
        ], $attributeGroup);
    }

    /**
     * @expectedException \Akeneo\Pim\Exception\NotFoundHttpException
     */
    public function testGetNotFound()
    {
        $api = $this->createClient()->getAttributeGroupApi();

        $api->get('unknown');
    }
}
