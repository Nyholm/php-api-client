<?php

namespace Akeneo\Pim\tests\Api\AttributeOption;

use Akeneo\Pim\tests\Api\ApiTestCase;

class UpsertAttributeOptionIntegration extends ApiTestCase
{
    public function testUpsertDoingUpdate()
    {
        $api = $this->createClient()->getAttributeOptionApi();

        $response = $api->upsert('weather_conditions', 'hot', [
            'sort_order' => 34,
            'labels'     => [
                'en_US' => 'Hot !',
            ],
        ]);

        $this->assertSame(204, $response);

        $attributeOption = $api->get('weather_conditions', 'hot');
        $this->assertSameContent([
            'code'       => 'hot',
            'attribute'  => 'weather_conditions',
            'sort_order' => 34,
            'labels'     => [
                'en_US' => 'Hot !',
            ],
        ], $attributeOption);
    }

    public function testUpsertDoingCreate()
    {
        $api = $this->createClient()->getAttributeOptionApi();
        $response = $api->upsert('size', '45', [
            'sort_order' => 9,
            'labels'     => [
                'en_US' => '45',
            ],
        ]);

        $this->assertSame(201, $response);

        $attributeOption = $api->get('size', '45');
        $this->assertSameContent([
            'code'       => '45',
            'attribute'  => 'size',
            'sort_order' => 9,
            'labels'     => [
                'en_US' => '45',
            ],
        ], $attributeOption);
    }

    /**
     * @expectedException \Akeneo\Pim\Exception\UnprocessableEntityHttpException
     */
    public function testUpsertWrongDataTypeFail()
    {
        $api = $this->createClient()->getAttributeOptionApi();
        $api->upsert('weather_conditions', 'hot', [
            'sort_order' => 34,
            'labels'     => [
                'en_US' => ['invalid type'],
            ],
        ]);
    }

    /**
     * @expectedException \Akeneo\Pim\Exception\UnprocessableEntityHttpException
     */
    public function testUpsertInvalidCodeFail()
    {
        $api = $this->createClient()->getAttributeOptionApi();
        $api->upsert('size', 'invalid code !', [
            'sort_order' => 9,
            'labels'     => [
                'en_US' => '45',
            ],
        ]);
    }

    /**
     * @expectedException \Akeneo\Pim\Exception\NotFoundHttpException
     */
    public function testUpsertOnAnUnknownAttribute()
    {
        $api = $this->createClient()->getAttributeOptionApi();
        $api->upsert('unknown', 'hot', [
            'sort_order' => 34,
            'labels'     => [
                'en_US' => 'Hot !',
            ],
        ]);
    }
}
