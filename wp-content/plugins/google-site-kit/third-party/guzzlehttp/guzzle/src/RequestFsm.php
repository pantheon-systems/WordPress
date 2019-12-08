<?php

namespace Google\Site_Kit_Dependencies\GuzzleHttp;

use Google\Site_Kit_Dependencies\GuzzleHttp\Event\BeforeEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\ErrorEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Event\EndEvent;
use Google\Site_Kit_Dependencies\GuzzleHttp\Exception\StateException;
use Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse;
use Google\Site_Kit_Dependencies\GuzzleHttp\Message\MessageFactoryInterface;
use Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface;
/**
 * Responsible for transitioning requests through lifecycle events.
 */
class RequestFsm
{
    private $handler;
    private $mf;
    private $maxTransitions;
    public function __construct(callable $handler, \Google\Site_Kit_Dependencies\GuzzleHttp\Message\MessageFactoryInterface $messageFactory, $maxTransitions = 200)
    {
        $this->mf = $messageFactory;
        $this->maxTransitions = $maxTransitions;
        $this->handler = $handler;
    }
    /**
     * Runs the state machine until a terminal state is entered or the
     * optionally supplied $finalState is entered.
     *
     * @param Transaction $trans      Transaction being transitioned.
     *
     * @throws \Exception if a terminal state throws an exception.
     */
    public function __invoke(\Google\Site_Kit_Dependencies\GuzzleHttp\Transaction $trans)
    {
        $trans->_transitionCount = 0;
        if (!$trans->state) {
            $trans->state = 'before';
        }
        transition:
        if (++$trans->_transitionCount > $this->maxTransitions) {
            throw new \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\StateException("Too many state transitions were " . "encountered ({$trans->_transitionCount}). This likely " . "means that a combination of event listeners are in an " . "infinite loop.");
        }
        switch ($trans->state) {
            case 'before':
                goto before;
            case 'complete':
                goto complete;
            case 'error':
                goto error;
            case 'retry':
                goto retry;
            case 'send':
                goto send;
            case 'end':
                goto end;
            default:
                throw new \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\StateException("Invalid state: {$trans->state}");
        }
        before:
        try {
            $trans->request->getEmitter()->emit('before', new \Google\Site_Kit_Dependencies\GuzzleHttp\Event\BeforeEvent($trans));
            $trans->state = 'send';
            if ((bool) $trans->response) {
                $trans->state = 'complete';
            }
        } catch (\Exception $e) {
            $trans->state = 'error';
            $trans->exception = $e;
        }
        goto transition;
        complete:
        try {
            if ($trans->response instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Ring\Future\FutureInterface) {
                // Futures will have their own end events emitted when
                // dereferenced.
                return;
            }
            $trans->state = 'end';
            $trans->response->setEffectiveUrl($trans->request->getUrl());
            $trans->request->getEmitter()->emit('complete', new \Google\Site_Kit_Dependencies\GuzzleHttp\Event\CompleteEvent($trans));
        } catch (\Exception $e) {
            $trans->state = 'error';
            $trans->exception = $e;
        }
        goto transition;
        error:
        try {
            // Convert non-request exception to a wrapped exception
            $trans->exception = \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException::wrapException($trans->request, $trans->exception);
            $trans->state = 'end';
            $trans->request->getEmitter()->emit('error', new \Google\Site_Kit_Dependencies\GuzzleHttp\Event\ErrorEvent($trans));
            // An intercepted request (not retried) transitions to complete
            if (!$trans->exception && $trans->state !== 'retry') {
                $trans->state = 'complete';
            }
        } catch (\Exception $e) {
            $trans->state = 'end';
            $trans->exception = $e;
        }
        goto transition;
        retry:
        $trans->retries++;
        $trans->response = null;
        $trans->exception = null;
        $trans->state = 'before';
        goto transition;
        send:
        $fn = $this->handler;
        $trans->response = \Google\Site_Kit_Dependencies\GuzzleHttp\Message\FutureResponse::proxy($fn(\Google\Site_Kit_Dependencies\GuzzleHttp\RingBridge::prepareRingRequest($trans)), function ($value) use($trans) {
            \Google\Site_Kit_Dependencies\GuzzleHttp\RingBridge::completeRingResponse($trans, $value, $this->mf, $this);
            $this($trans);
            return $trans->response;
        });
        return;
        end:
        $trans->request->getEmitter()->emit('end', new \Google\Site_Kit_Dependencies\GuzzleHttp\Event\EndEvent($trans));
        // Throw exceptions in the terminal event if the exception
        // was not handled by an "end" event listener.
        if ($trans->exception) {
            if (!$trans->exception instanceof \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException) {
                $trans->exception = \Google\Site_Kit_Dependencies\GuzzleHttp\Exception\RequestException::wrapException($trans->request, $trans->exception);
            }
            throw $trans->exception;
        }
    }
}
