<?php

namespace Drengr\Controller;

use Drengr\Exception\ModelNotFoundException;
use Drengr\Framework\AuthenticationService;
use Drengr\Framework\Traits\AdminAccess;
use Drengr\Framework\Traits\GetTokenFromHeader;
use Drengr\Framework\Traits\RestController;
use Drengr\Repository\MemberRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class MemberRestController extends AbstractRestController
{
    use AdminAccess;

//    use GetTokenFromHeader;
    use RestController;

    /** @var MemberRepository */
    private $repository;

    /** @var AuthenticationService */
    private $service;

    public function __construct(MemberRepository $repository, AuthenticationService $service)
    {
        $this->repository = $repository;
        $this->service = $service;

        $this->namespace = 'drengr/v1';
        $this->rest_base = 'member';
    }

    /**
     * Prepares one item for create or update operation.
     *
     * @param WP_REST_Request $request Request object.
     * @return object|WP_Error The prepared item, or WP_Error object on failure.
     * @since 4.7.0
     */
    protected function prepare_item_for_database($request)
    {
        return (object)['pretend' => 'stuff'];
    }

    /**
     * Register a route for the currently logged in user, then register all of the
     * member entity REST routes.
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/me',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'me'],
                    'permission_callback' => __return_true, // [$this, 'get_items_permissions_check'],
                    'args' => $this->get_collection_params(),
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );

        parent::register_routes();
    }

    /**
     * Return details about the currently logged in user.
     *
     * @param WP_REST_Request $request
     * @return WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function me(WP_REST_Request $request)
    {
        try {
            $token = $request->get_header('Authorization');
            if (strpos(strtolower($token), 'bearer') === 0) {
                $token = substr($token, 7);
            }
            $user = $this->service->validateToken($token);

            $item = $this->repository->findByWpUserOrFail($user);

            return $this->buildResponse([$item], $request);
        } catch (ModelNotFoundException $e) {
            return new WP_Error('Not Found', 'Member was not found.', ['status' => 404]);
        } catch (\Exception $e) {
            return new WP_Error('Exception', $e->getMessage(), ['status' => empty($e->getCode()) ? 500 : $e->getCode()]);
        }
    }

    /**
     * Prepares the item for the REST response. Called from $this->buildResponse
     * with one item at a time.
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function prepare_item_for_response($item, $request)
    {
        $member = array_diff_assoc($item, ['wp_user_id', 'wp_user_validated']);
        $member['certifications'] = $this->repository->getCertifications($member['id']);
        $member['email'] = $this->repository->getEmail($member['id']);
        $member['phone'] = $this->repository->getPhone($member['id']);
        $member['rank'] = $this->repository->getRanks($member['id']);
        return $member;
    }
}
