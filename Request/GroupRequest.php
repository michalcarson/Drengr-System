<?php

namespace Drengr\Request;

use Drengr\Framework\Request;
use Drengr\Framework\Validator;

class GroupRequest extends Request
{
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
