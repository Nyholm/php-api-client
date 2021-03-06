<?php

namespace Akeneo\Pim\Api;

use Akeneo\Pim\Client\ResourceClientInterface;
use Akeneo\Pim\Exception\InvalidArgumentException;
use Akeneo\Pim\Pagination\PageFactoryInterface;
use Akeneo\Pim\Pagination\ResourceCursorFactoryInterface;

/**
 * API implementation to manage the attribute options.
 *
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeOptionApi implements AttributeOptionApiInterface
{
    const ATTRIBUTE_OPTIONS_URI = 'api/rest/v1/attributes/%s/options';
    const ATTRIBUTE_OPTION_URI = 'api/rest/v1/attributes/%s/options/%s';

    /** @var ResourceClientInterface */
    protected $resourceClient;

    /** @var PageFactoryInterface */
    protected $pageFactory;

    /** @var ResourceCursorFactoryInterface */
    protected $cursorFactory;

    /**
     * @param ResourceClientInterface        $resourceClient
     * @param PageFactoryInterface           $pageFactory
     * @param ResourceCursorFactoryInterface $cursorFactory
     */
    public function __construct(
        ResourceClientInterface $resourceClient,
        PageFactoryInterface $pageFactory,
        ResourceCursorFactoryInterface $cursorFactory
    ) {
        $this->resourceClient = $resourceClient;
        $this->pageFactory = $pageFactory;
        $this->cursorFactory = $cursorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode, $code)
    {
        return $this->resourceClient->getResource(static::ATTRIBUTE_OPTION_URI, [$attributeCode, $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function listPerPage($attributeCode, $limit = 10, $withCount = false, array $queryParameters = [])
    {
        $uri = sprintf(static::ATTRIBUTE_OPTIONS_URI, $attributeCode);
        $data = $this->resourceClient->getResources($uri, [], $limit, $withCount, $queryParameters);

        return $this->pageFactory->createPage($data);
    }

    /**
     * {@inheritdoc}
     */
    public function all($attributeCode, $pageSize = 10, array $queryParameters = [])
    {
        $firstPage = $this->listPerPage($attributeCode, $pageSize, false, $queryParameters);

        return $this->cursorFactory->createCursor($pageSize, $firstPage);
    }

    /**
     * {@inheritdoc}
     */
    public function create($attributeCode, $attributeOptionCode, array $data = [])
    {
        if (array_key_exists('code', $data)) {
            throw new InvalidArgumentException('The parameter "code" should not be defined in the data parameter');
        }

        if (array_key_exists('attribute', $data)) {
            throw new InvalidArgumentException('The parameter "attribute" should not be defined in the data parameter');
        }

        $data['code'] = $attributeOptionCode;
        $data['attribute'] = $attributeCode;

        return $this->resourceClient->createResource(static::ATTRIBUTE_OPTIONS_URI, [$attributeCode], $data);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert($attributeCode, $attributeOptionCode, array $data = [])
    {
        return $this->resourceClient->upsertResource(static::ATTRIBUTE_OPTION_URI, [$attributeCode, $attributeOptionCode], $data);
    }
}
