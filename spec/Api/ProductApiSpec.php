<?php

namespace spec\Akeneo\Pim\Api;

use Akeneo\Pim\Api\ListableResourceInterface;
use Akeneo\Pim\Api\ProductApi;
use Akeneo\Pim\Api\ProductApiInterface;
use Akeneo\Pim\Client\ResourceClientInterface;
use Akeneo\Pim\Exception\InvalidArgumentException;
use Akeneo\Pim\Pagination\PageInterface;
use Akeneo\Pim\Pagination\PageFactoryInterface;
use Akeneo\Pim\Pagination\ResourceCursorFactoryInterface;
use Akeneo\Pim\Pagination\ResourceCursorInterface;
use Akeneo\Pim\Stream\UpsertResourceListResponse;
use PhpSpec\ObjectBehavior;

class ProductApiSpec extends ObjectBehavior
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
        $this->shouldHaveType(ProductApi::class);
        $this->shouldImplement(ProductApiInterface::class);
        $this->shouldImplement(ListableResourceInterface::class);
    }

    function it_returns_a_product($resourceClient)
    {
        $productCode = 'foo';
        $product = [
            'identifier' => 'foo',
            'family' => 'tshirts',
            'enabled' => true,
            'categories' => [
                'bar'
            ],
        ];

        $resourceClient
            ->getResource(ProductApi::PRODUCT_URI, [$productCode])
            ->willReturn($product);

        $this->get($productCode)->shouldReturn($product);
    }

    function it_returns_a_list_of_products_with_default_parameters($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(ProductApi::PRODUCTS_URI, [], 10, false, [])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage()->shouldReturn($page);
    }

    function it_returns_a_list_of_products_with_limit_and_count($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(ProductApi::PRODUCTS_URI, [], 10, true, [])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage(10, true)->shouldReturn($page);
    }

    function it_returns_a_cursor_on_the_list_of_products(
        $resourceClient,
        $pageFactory,
        $cursorFactory,
        PageInterface $page,
        ResourceCursorInterface $cursor
    ) {
        $resourceClient
            ->getResources(ProductApi::PRODUCTS_URI, [], 10, false, ['pagination_type' => 'search_after'])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $cursorFactory->createCursor(10, $page)->willReturn($cursor);

        $this->all(10, [])->shouldReturn($cursor);
    }

    function it_returns_a_list_of_products_with_additional_query_parameters($resourceClient, $pageFactory, PageInterface $page)
    {
        $resourceClient
            ->getResources(ProductApi::PRODUCTS_URI, [], null, null, ['foo' => 'bar'])
            ->willReturn([]);

        $pageFactory->createPage([])->willReturn($page);

        $this->listPerPage(null, null, ['foo' => 'bar'])->shouldReturn($page);
    }

    function it_creates_a_product($resourceClient)
    {
        $resourceClient
            ->createResource(
                ProductApi::PRODUCTS_URI,
                [],
                ['identifier' => 'foo', 'family' => 'bar']
            )
            ->willReturn(201);

        $this->create('foo', ['family' => 'bar'])->shouldReturn(201);
    }

    function it_throws_an_exception_if_identifier_is_provided_in_data_when_creating_a_product()
    {
        $this
            ->shouldThrow(new InvalidArgumentException('The parameter "identifier" should not be defined in the data parameter'))
            ->during('create', ['foo', ['identifier' => 'foo', 'family' => 'bar']]);
    }

    function it_upserts_a_product($resourceClient)
    {
        $resourceClient
            ->upsertResource(ProductApi::PRODUCT_URI, ['foo'], ['identifier' => 'foo' , 'family' => 'bar'])
            ->willReturn(204);

        $this->upsert('foo', ['identifier' => 'foo' , 'family' => 'bar'])
            ->shouldReturn(204);
    }

    function it_deletes_a_product($resourceClient)
    {
        $resourceClient
            ->deleteResource(ProductApi::PRODUCT_URI, ['foo'])
            ->willReturn(204);

        $this->delete('foo')->shouldReturn(204);
    }

    function it_upserts_a_list_of_products($resourceClient, UpsertResourceListResponse $response)
    {
        $resourceClient
            ->upsertResourceList(
                ProductApi::PRODUCTS_URI,
                [],
                [
                    ['identifier' => 'identifier_1'],
                    ['identifier' => 'identifier_2'],
                    ['identifier' => 'identifier_3'],
                ]
            )
            ->willReturn($response);

        $this
            ->upsertList([
                ['identifier' => 'identifier_1'],
                ['identifier' => 'identifier_2'],
                ['identifier' => 'identifier_3'],
            ])->shouldReturn($response);
    }
}
