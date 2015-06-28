<?php
namespace Castle23;

use King23\Core\SettingsInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StaticFileMiddleware
{

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // lets see if this file exists within a static context
        $testFor = $this->settings->get('app.webroot', 'public');
        $testFor .= $request->getUri()->getPath();

        if ($this->isWithinPublicPath($testFor) && !is_dir($testFor) && substr($testFor, -3) != 'php') {
            // this sucks as it is blocking!
            $response->withStatus(200)->getBody()->write(file_get_contents($testFor));
            $response->withHeader('Content-Type', $this->getMime($testFor));

            // we quit the queue here, as we already have our response
            return $response;
        }

        $response = $next($request, $response);

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

