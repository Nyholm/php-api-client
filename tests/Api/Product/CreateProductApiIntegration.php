<?php

namespace Akeneo\Pim\tests\Api\Product;

use Akeneo\Pim\Exception\UnprocessableEntityHttpException;

class CreateProductApiIntegration extends AbstractProductApiTestCase
{
    public function testCreate()
    {
        $api = $this->createClient()->getProductApi();
        $response = $api->create('pumps', [
            'enabled'    => false,
            'family'     => 'sandals',
            'categories' => ['summer_collection'],
            'values'     => [
                'name' => [
                    [
                        'data'   => 'The pumps',
                        'locale' => 'en_US',
                        'scope'  => null,
                    ],
                    [
                        'data'   => 'Les pumps',
                        'locale' => 'fr_FR',
                        'scope'  => null,
                    ]
                ],
            ]
        ]);

        $this->assertSame(201, $response);

        $expectedProduct = $this->sanitizeProductData([
            'identifier'    => 'pumps',
            'family'        => 'sandals',
            'groups'        => [
            ],
            'variant_group' => null,
            'categories'    => [
                'summer_collection',
            ],
            'enabled'       => false,
            'values'        => [
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope'  => null,
                        'data'   => 'The pumps',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope'  => null,
                        'data'   => 'Les pumps',
                    ],
                ],
            ],
            'created'       => '2017-06-26T14:27:19+00:00',
            'updated'       => '2017-06-26T14:27:19+00:00',
            'associations'  => [
            ],
        ]);

        $product = $this->sanitizeProductData($api->get('pumps'));

        $this->assertSameContent($expectedProduct, $product);
    }

    public function testCreateAnExistingProduct()
    {
        $api = $this->createClient()->getProductApi();

        try {
            $api->create('black_sneakers', [
                'enabled'    => false,
                'family'     => 'sneakers',
                'categories' => ['summer_collection'],
                'values'     => [
                    'name' => [
                        [
                            'locale' => 'en_US',
                            'data'   => 'Black sneakers',
                            'scope'  => null,
                        ],
                        [
                            'data'   => 'Sneakers',
                            'locale' => 'fr_FR',
                            'scope'  => null,
                        ],
                    ],
                ]
            ]);
        } catch (UnprocessableEntityHttpException $exception) {
            $this->assertSame([
                [
                    'property' => 'identifier',
                    'message'  => 'The value black_sneakers is already set on another product for the unique attribute sku',
                ],
            ], $exception->getResponseErrors());
        }
    }

    /**
     * @expectedException \Akeneo\Pim\Exception\UnprocessableEntityHttpException
     */
    public function testCreateAnInvalidProduct()
    {
        $api = $this->createClient()->getProductApi();
        $api->create('pumps', [
            'enabled'    => false,
            'family'     => 'unknown_family',
            'categories' => ['summer_collection'],
            'values'     => [
                'name' => [
                    [
                        'data'   => 'The pumps',
                        'locale' => 'en_US',
                        'scope'  => null,
                    ],
                    [
                        'data'   => 'Les pumps',
                        'locale' => 'fr_FR',
                        'scope'  => null,
                    ]
                ],
            ]
        ]);
    }
}
