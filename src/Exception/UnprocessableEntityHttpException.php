<?php

namespace Akeneo\Pim\Exception;

/**
 * Exception thrown when it is the request is unprocessable (422).
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UnprocessableEntityHttpException extends ClientErrorHttpException
{
    /**
     * Returns the errors of the response if there are any
     *
     * @return array
     */
    public function getResponseErrors()
    {
        $responseBody = $this->response->getBody();

        $responseBody->rewind();
        $decodedBody = json_decode($responseBody->getContents(), true);
        $responseBody->rewind();

        return isset($decodedBody['errors']) ? $decodedBody['errors'] : [];
    }
}
