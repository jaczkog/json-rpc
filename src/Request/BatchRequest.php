<?php

namespace JsonRpc\Request;

use JsonRpc\JsonRpc;

class BatchRequest extends AbstractRequest
{
    /**
     * @var Request[]
     */
    private $requests;

    /**
     * BatchRequest constructor.
     *
     * @param array|Request[] $requests
     */
    public function __construct(array $requests)
    {
        $this->requests = array();

        foreach ($requests as $request) {
            if (!$request instanceof Request) {
                $request = Request::fromArray($request);
            }
            $this->requests[] = $request;
        }
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public function toJson($version = JsonRpc::VER_1)
    {
        return json_encode(
            array_map(
                function ($request) use ($version) {
                    /** @var Request $request */
                    return $request->toArray($version);
                },
                $this->requests
            )
        );
    }
}
