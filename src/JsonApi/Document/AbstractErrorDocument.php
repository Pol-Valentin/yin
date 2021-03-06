<?php
namespace WoohooLabs\Yin\JsonApi\Document;

use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\Schema\Error;

abstract class AbstractErrorDocument extends AbstractDocument
{
    /**
     * @var \WoohooLabs\Yin\JsonApi\Schema\Error[]
     */
    protected $errors = [];

    /**
     * @return \WoohooLabs\Yin\JsonApi\Schema\Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Includes a new error in the error document.
     *
     * @param \WoohooLabs\Yin\JsonApi\Schema\Error $error
     * @return $this
     */
    public function addError(Error $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Returns a response with a status code of $responseCode, containing all the provided sections of the error
     * document. You can also pass additional meta information for the document in the $additionalMeta argument.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param int $responseCode
     * @param array $additionalMeta
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(ResponseInterface $response, $responseCode = null, array $additionalMeta = [])
    {
        $response = $response->withStatus($this->getResponseCode($responseCode));
        $response = $response->withAddedHeader("Content-Type", $this->getContentType());
        $response->getBody()->rewind();
        $response->getBody()->write(json_encode($this->transformContent($additionalMeta)));

        return $response;
    }

    /**
     * @param int $responseCode
     * @return int
     */
    protected function getResponseCode($responseCode)
    {
        if ($responseCode !== null) {
            return $responseCode;
        }

        if (count($this->errors) === 1) {
            return $this->errors[0]->getStatus();
        }

        $responseCode = 500;
        foreach ($this->errors as $error) {
            /** @var \WoohooLabs\Yin\JsonApi\Schema\Error $error */
            $roundedStatusCode = intval($error->getStatus() / 100) * 100;

            if (abs($responseCode - $roundedStatusCode) >= 100) {
                $responseCode = $roundedStatusCode;
            }
        }

        return $responseCode;
    }

    /**
     * @param array $additionalMeta
     * @return array
     */
    public function transformContent(array $additionalMeta = [])
    {
        $content = $this->transformBaseContent($additionalMeta);

        // ERRORS
        if (empty($this->errors) === false) {
            foreach ($this->errors as $error) {
                /** @var \WoohooLabs\Yin\JsonApi\Schema\Error $error */
                $content["errors"][] = $error->transform();
            }
        }

        return $content;
    }
}
