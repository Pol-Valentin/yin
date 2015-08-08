<?php
namespace WoohooLabs\Yin\Examples\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\Examples\JsonApi\Document\UserDocument;
use WoohooLabs\Yin\Examples\JsonApi\Resource\ContactResourceTransformer;
use WoohooLabs\Yin\Examples\JsonApi\Resource\UserResourceTransformer;
use WoohooLabs\Yin\Examples\Repository\UserRepository;
use WoohooLabs\Yin\JsonApi\Request\Request;
use WoohooLabs\Yin\JsonApi\Response\FetchRelationshipResponse;

class UserRelationships
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
        } else {
            die("You must define the 'id' query parameter with a value of '1' or '2'!");
        }

        if (isset($_GET["relationship"])) {
            $relationshipName = $_GET["relationship"];
        } else {
            die("You must define the 'relationship' query parameter with a value of 'contacts'!");
        }

        $resource = UserRepository::getUser($id);

        $document = new UserDocument(new UserResourceTransformer(new ContactResourceTransformer()));

        return FetchRelationshipResponse::ok(
            $relationshipName,
            Request::fromServerRequest($request),
            $response,
            $document,
            $resource
        );
    }
}
