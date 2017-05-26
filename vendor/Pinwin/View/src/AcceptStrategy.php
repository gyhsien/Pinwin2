<?php
namespace Pinwin\View;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Feed\Writer\Feed;
use Zend\View\Renderer\FeedRenderer;
use Zend\View\Renderer\JsonRenderer;
use Zend\View\Renderer\PhpRenderer;

class AcceptStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    protected $feedRenderer;
    protected $jsonRenderer;
    protected $phpRenderer;

    public function __construct(
        PhpRenderer $phpRenderer,
        JsonRenderer $jsonRenderer,
        FeedRenderer $feedRenderer
        ) {
            $this->phpRenderer  = $phpRenderer;
            $this->jsonRenderer = $jsonRenderer;
            $this->feedRenderer = $feedRenderer;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach('renderer', [$this, 'selectRenderer'], $priority);
        $this->listeners[] = $events->attach('response', [$this, 'injectResponse'], $priority);
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function selectRenderer($e)
    {
        $request = $e->getRequest();
        $headers = $request->getHeaders();

        // No Accept header? return PhpRenderer
        if (!$headers->has('accept')) {
            return $this->phpRenderer;
        }

        $accept = $headers->get('accept');
        foreach ($accept->getPrioritized() as $mediaType) {
            if (0 === strpos($mediaType, 'application/json')) {
                return $this->jsonRenderer;
            }

            if (0 === strpos($mediaType, 'application/rss+xml')) {
                $this->feedRenderer->setFeedType('rss');
                return $this->feedRenderer;
            }

            if (0 === strpos($mediaType, 'application/atom+xml')) {
                $this->feedRenderer->setFeedType('atom');
                return $this->feedRenderer;
            }
        }

        // Nothing matched; return PhpRenderer. Technically, we should probably
        // return an HTTP 415 Unsupported response.
        return $this->phpRenderer;
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $e The MvcEvent instance
     * @return void
     */
    public function injectResponse($e)
    {
        $renderer = $e->getRenderer();
        $response = $e->getResponse();
        $result   = $e->getResult();

        if ($renderer === $this->jsonRenderer) {
            // JSON Renderer; set content-type header
            $headers = $response->getHeaders();
            $headers->addHeaderLine('content-type', 'application/json');
            $response->setContent($result);
            return;
        }

        if ($renderer === $this->feedRenderer) {
            // Feed Renderer; set content-type header, and export the feed if
            // necessary
            $feedType  = $this->feedRenderer->getFeedType();
            $headers   = $response->getHeaders();
            $mediatype = 'application/'
                . (('rss' == $feedType) ? 'rss' : 'atom')
                . '+xml';
                $headers->addHeaderLine('content-type', $mediatype);

                // If the $result is a feed, export it
                if ($result instanceof Feed) {
                    $result = $result->export($feedType);
                }

                $response->setContent($result);
                return;
        }
    }
}