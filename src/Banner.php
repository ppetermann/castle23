<?php
namespace Castle23;

use Knight23\Core\Colors\Colors;
use Knight23\Core\RunnerInterface;

class Banner extends \Knight23\Core\Banner\Banner
{
    /**
     * @var RunnerInterface
     */
    private $runner;

    /**
     * @param RunnerInterface $runner
     */
    public function __construct(RunnerInterface $runner)
    {
        $this->runner = $runner;
    }

    /**
     * @return string
     */
    public function getBanner()
    {
        $bannerText = <<<EOT

   (                 )  (           )     )
   )\      )      ( /(  )\   (   ( /(  ( /(
 (((_)  ( /(  (   )\())((_) ))\  )(_)) )\())
 )\___  )(_)) )\ (_))/  _  /((_)((_)  ((_)\
((/ __|((_)_ ((_)| |_  | |(_))  |_  )|__ (_)
 | (__ / _` |(_-<|  _| | |/ -_)  / /  |_ \
  \___|\__,_|/__/ \__| |_|\___| /___||___/

EOT;

        $version = str_pad("Version: ".$this->runner->getVersion(), 45, " ", STR_PAD_BOTH);
        return Colors::COLOR_FG_RED.$bannerText.Colors::RESET."\n".Colors::COLOR_FGL_YELLOW.$version.Colors::RESET;
    }
}
