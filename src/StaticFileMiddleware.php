<?php
namespace Castle23;

use King23\Core\SettingsInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StaticFileMiddleware implements MiddlewareInterface
{

    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @param SettingsInterface $settings
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(SettingsInterface $settings, ResponseFactoryInterface $responseFactory)
    {
        $this->settings = $settings;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface
    {
        // lets see if this file exists within a static context
        $testFor = $this->settings->get('app.webroot', 'public');
        $testFor .= $request->getUri()->getPath();

        if ($this->isWithinPublicPath($testFor) && !is_dir($testFor) && substr($testFor, -3) != 'php') {
            $response = $this->responseFactory->createResponse(200);
            // this sucks as it is blocking!
            $response->getBody()->write(file_get_contents($testFor));
            $response->withHeader('Content-Type', $this->getMime($testFor));

            // we quit the queue here, as we already have our response
            return $response;
        }

        $response = $next->handle($request);

        return $response;
    }

    /**
     * determine mimetype
     *
     * @param $filename
     * @return string
     */
    public function getMime($filename)
    {
        switch (substr($filename, -3)) {
            case "css":
                $mime = "text/css";
                break;
            case ".js":
                $mime = "text/javascript";
                break;
            default:
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($filename);

        }

        return $mime;
    }

    private function isWithinPublicPath($testFor)
    {
        $testFor = realpath($testFor);
        $webroot = realpath($this->settings->get('app.webroot', 'public'));

        if (substr($testFor, 0, strlen($webroot)) == $webroot) {
            return file_exists($testFor);
        }
        return false;
    }
}

