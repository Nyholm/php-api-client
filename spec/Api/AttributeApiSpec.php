<?php

namespace spec\Akeneo\Pim\Api;

use Akeneo\Pim\Api\AttributeApi;
use Akeneo\Pim\Api\AttributeApiInterface;
use Akeneo\Pim\Api\ListableResourceInterface;
use Akeneo\Pim\Client\ResourceClientInterface;
use Akeneo\Pim\Exception\InvalidArgumentException;
use Akeneo\Pim\Pagination\PageFactoryInterface;
use Akeneo\Pim\Pagination\PageInterface;
use Akeneo\Pim\Pagination\ResourceCursorFactoryInterface;
use Akeneo\Pim\Pagination\ResourceCursorInterface;
use Akeneo\Pim\Stream\UpsertResourceListResponse;
use PhpSpec\ObjectBehavior;

class AttributeApiSpec extends ObjectBehavior
{
    function let(
        ResourceClientInterface $resourceClient,
        PageFactoryInterface $pageFactory,
        ResourceCursorFactoryInterface $cursorFactory
    ) {
        $this->beConstructedWith($resourceClient, $pageFactory, $cursorFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeApi::class);
        $this->shouldImplement(AttributeApiInterface::class);
        $this->shouldImplement(ListableResourceInterface::class);
    }

    function it_returns_an_attribute($resourceClient)
    {
        $attributeCode = 'foo';
        $attribute = [
            'code' => 'foo',
            'type' => 'pim_catalog_text',
            'unique' => false,
            'labels' => [
                'en_US' => 'Foo',
            ],
        ];

        $resourceClient
            ->getResource(AttributeApi::ATTRIBUTE_URI, [$attributeCode])
            ->willReturn($attribute);

        $this->get($attributeCode)->shouldReturn($attribute);
    }

    function it_returns_a_list_of_attributes_with_default_parameters($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(AttributeApi::ATTRIBUTES_URI, [], 10, false, [])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage()->shouldReturn($page);
    }

    function it_returns_a_list_of_attributes_with_limit_and_count($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(AttributeApi::ATTRIBUTES_URI, [], 10, true, [])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage(10, true)->shouldReturn($page);
    }

    function it_returns_a_cursor_on_the_list_of_attributes(
        $resourceClient,
        $pageFactory,
        $cursorFactory,
        PageInterface $page,
        ResourceCursorInterface $cursor
    ) {
        $resourceClient
            ->getResources(AttributeApi::ATTRIBUTES_URI, [], 10, false, [])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $cursorFactory->createCursor(10, $page)->willReturn($cursor);

        $this->all(10, [])->shouldReturn($cursor);
    }

    function it_returns_a_list_of_attributes_with_additional_query_parameters($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(AttributeApi::ATTRIBUTES_URI, [], null, null, ['foo' => 'bar'])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage(null, null, ['foo' => 'bar'])->shouldReturn($page);
    }

    function it_creates_an_attribute($resourceClient)
    {
        $resourceClient
            ->createResource(
                AttributeApi::ATTRIBUTES_URI,
                [],
                ['code' => 'foo', 'type' => 'pim_catalog_text', 'group' => 'bar']
            )
            ->willReturn(201);

        $this->create('foo', ['type' => 'pim_catalog_text', 'group' => 'bar'])->shouldReturn(201);
    }

    function it_throws_an_exception_if_code_is_provided_in_data_when_creating_an_attribute()
    {
        $this->shouldThrow(new InvalidArgumentException('The parameter "code" should not be defined in the data parameter'))->during(
            'create', ['foo', ['code' => 'foo', 'type' => 'pim_catalog_text', 'group' => 'bar']]
        );
    }

    function it_upserts_an_attribute($resourceClient)
    {
        $resourceClient
            ->upsertResource(AttributeApi::ATTRIBUTE_URI, ['foo'], ['code' => 'foo', 'type' => 'pim_catalog_text', 'group' => 'bar'])
            ->willReturn(204);

        $this
            ->upsert('foo', ['code' => 'foo', 'type' => 'pim_catalog_text', 'group' => 'bar'])
            ->shouldReturn(204);
    }

    function it_upserts_a_list_of_attributes($resourceClient, UpsertResourceListResponse $response)
    {
        $resourceClient
            ->upsertResourceList(
                AttributeApi::ATTRIBUTES_URI,
                [],
                [
                    ['code' => 'attribute_1'],
                    ['code' => 'attribute_2'],
                    ['code' => 'attribute_3'],
                ]
            )
            ->willReturn($response);

        $this
            ->upsertList([
                ['code' => 'attribute_1'],
                ['code' => 'attribute_2'],
                ['code' => 'attribute_3'],
            ])->shouldReturn($response);
    }
}
