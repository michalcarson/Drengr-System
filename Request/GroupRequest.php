<?php

namespace Drengr\Request;

use Drengr\Framework\Request;
use Drengr\Framework\Validator;

class GroupRequest extends Request
{
    /**
     * Set the rules for validation while considering whether this is a `create`
     * or an `update` action based on the HTTP verb used.
     *
     * @param Validator $validator
     */
    protected function setRules(Validator $validator)
    {
        if ($this->getMethod() === 'post') {
            $validator->setRules('name', ['required', 'string']);
            $validator->setRules('url', ['string', 'url']);
        } else {
            $validator->setRules('name', ['string']);
            $validator->setRules('url', ['string', 'url']);
        }
    }
}
