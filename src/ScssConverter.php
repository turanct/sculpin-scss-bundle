<?php
/**
 * This file is part of Sculpin Scss Bundle.
 *
 * (c) DevWorks Greece
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevWorks\Sculpin\Bundle\ScssBundle;

use Leafo\ScssPhp\Compiler;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\AbstractSource;
use Sculpin\Core\Source\FileSource;
use Sculpin\Core\Source\MemorySource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Scss Converter.
 *
 * @author Ioannis Kappas <ikappas@devworks.gr>
 */
class ScssConverter implements EventSubscriberInterface
{
    /**
     * Formatter
     *
     * @var string
     */
    protected $formatter;

    /**
     * Extensions
     *
     * @var string[]
     */
    protected $extensions = array();

    /**
     * Files
     *
     * @var string[]
     */
    protected $files = array();

    /**
     * Compiler.
     *
     * @var \Leafo\ScssPhp\Compiler
     */
    private $compiler;

    /**
     * @param string   $formatter
     * @param string[] $extensions
     * @param string[] $files
     */
    public function __construct($formatter, array $extensions = [], array $files = [])
    {
        $this->formatter = $formatter;
        $this->extensions = $extensions;
        $this->files = $files;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        );
    }

    /**
     * Get the SCSS compiler.
     *
     * @return \Leafo\ScssPhp\Compiler
     */
    protected function getCompiler()
    {
        if ($this->compiler === null) {
            $this->compiler = new Compiler();
            $this->compiler->setFormatter($this->formatter);
        }
        return $this->compiler;
    }

    /**
     * Before run
     *
     * @param SourceSetEvent $sourceSetEvent Source Set Event
     */
    public function beforeRun(SourceSetEvent $sourceSetEvent)
    {
        $sourceSet = $sourceSetEvent->sourceSet();

        /** @var FileSource $source */
        foreach ($sourceSetEvent->updatedSources() as $source) {
            if (!$this->shouldBeConverted($source)) {
                continue;
            }

            $source->setShouldBeSkipped();

            $css = $this->compileScss($source->file()->getPathname());
            if (!$css) {
                continue;
            }

            $generatedSource = $this->createDuplicate($source);
            $generatedSource->setContent($css);

            $sourceSet->mergeSource($generatedSource);
        }
    }

    /**
     * @param AbstractSource $source
     * @return bool
     */
    private function shouldBeConverted(AbstractSource $source)
    {
        // File based whitelist has precedence
        if (!empty($this->files)) {
            foreach ($this->files as $fileName) {
                if ($source->relativePathname() === $fileName) {
                    return true;
                }
            }

            return false;
        }

        foreach ($this->extensions as $extension) {
            if (fnmatch("*.{$extension}", $source->filename())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function replaceFileExtension($fileName)
    {
        return str_replace('.scss', '.css', str_replace('.sass', '.css', $fileName));
    }

    /**
     * @param FileSource $source
     * @return MemorySource
     */
    private function createDuplicate(FileSource $source)
    {
        $options = [
            'filename'         => $this->replaceFileExtension($source->filename()),
            'relativePathname' => $this->replaceFileExtension($source->relativePathname()),
        ];

        $generatedSource = $source->duplicate(
            $source->sourceId() . ':' . 'css',
            $options
        );

        $generatedSource->setIsGenerated();

        return $generatedSource;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function compileScss($fileName)
    {
        $fileContents = file_get_contents($fileName);

        $scss = $this->getCompiler();
        $scss->setImportPaths(dirname($fileName));

        return $scss->compile($fileContents, $fileName);
    }
}
