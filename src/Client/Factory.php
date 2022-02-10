<?php

namespace Smokills\Http\Client;

use Illuminate\Http\Client\Factory as BaseFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;

class Factory extends BaseFactory
{
    protected $ignoreDefaultOptions = false;

    protected $defaultOptions = [];

    public function ignoreDefaultOptions()
    {
        $this->ignoreDefaultOptions = true;

        return $this;
    }

    public function withoutDefaultOptions($keys = null)
    {
        if ($keys === null) {
            return $this->ignoreDefaultOptions();
        }

        if (func_num_args() > 1) {
            $keys = func_get_args();
        }

        $this->defaultOptions = with($this->defaultOptions, function ($options) use ($keys) {
            foreach (Arr::wrap($keys) as $key) {
                Arr::forget($options, $key);
            }

            return $options;
        });

        return $this;
    }

    public function withDefaultOptions(array $options)
    {
        $this->defaultOptions = array_merge_recursive($this->defaultOptions, $options);

        return $this;
    }

    protected function newPendingRequest()
    {
        $request = new PendingRequest($this);

        if ($this->defaultOptions && ! $this->ignoreDefaultOptions) {
            $request->withOptions($this->defaultOptions);
        }

        return $request;
    }
}
