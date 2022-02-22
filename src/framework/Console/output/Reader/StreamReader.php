<?php

namespace framework\Console\output\Reader;

use framework\Exception\RuntimeException;

class StreamReader implements ReaderInterface
{
    protected $stream = false;
    private static $stty;
    private static $shell;

    /**
     * Read the line typed in by the user
     *
     * @return string
     */
    public function line()
    {
        return trim(fgets($this->getStream(), 1024));
    }

    /**
     * Read from STDIN until EOF (^D) is reached
     *
     * @return string
     */
    public function multiLine()
    {
        return trim(stream_get_contents($this->getStream()));
    }

    /**
     * Read one character
     *
     * @param int $count
     *
     * @return string
     */
    public function char($count = 1)
    {
        return fread($this->getStream(), $count);
    }

    /**
     * Return a valid STDIN, even if it previously EOF'ed
     *
     * Lazily re-opens STDIN after hitting an EOF
     *
     * @return resource
     * @throws RuntimeException
     */
    protected function getStream()
    {
        if ($this->stream && !feof($this->stream)) {
            return $this->stream;
        }

        try {
            $this->setStream();
        } catch (\Error $e) {
            throw new RuntimeException('Unable to read from STDIN', 0, $e);
        }

        return $this->stream;
    }

    /**
     * Attempt to set the stdin property
     *
     * @return void
     * @throws RuntimeException
     */
    protected function setStream()
    {
        if ($this->stream !== false) {
            fclose($this->stream);
        }

        $this->stream = fopen('php://stdin', 'r');

        if (!$this->stream) {
            throw new RuntimeException('Unable to read from stream');
        }
    }
	
	public function hidden($inputStream)
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $exe = __DIR__ . '/../../bin/hiddeninput.exe';

            $value = rtrim(shell_exec($exe));

            return $value;
        }

        if ($this->hasSttyAvailable()) {
            $sttyMode = shell_exec('stty -g');

            shell_exec('stty -echo');
            $value = fgets($inputStream, 4096);
            shell_exec(sprintf('stty %s', $sttyMode));

            if (false === $value) {
                throw new RuntimeException('Aborted');
            }

            $value = trim($value);

            return $value;
        }

        if (false !== $shell = $this->getShell()) {
            $readCmd = $shell === 'csh' ? 'set mypassword = $<' : 'read -r mypassword';
            $command = sprintf("/usr/bin/env %s -c 'stty -echo; %s; stty echo; echo \$mypassword'", $shell, $readCmd);
            $value   = rtrim(shell_exec($command));

            return $value;
        }

        throw new RuntimeException('Unable to hide the response.');
    }
	
    private function getShell()
    {
        if (null !== self::$shell) {
            return self::$shell;
        }

        self::$shell = false;

        if (file_exists('/usr/bin/env')) {
            $test = "/usr/bin/env %s -c 'echo OK' 2> /dev/null";
            foreach (['bash', 'zsh', 'ksh', 'csh'] as $sh) {
                if ('OK' === rtrim(shell_exec(sprintf($test, $sh)))) {
                    self::$shell = $sh;
                    break;
                }
            }
        }

        return self::$shell;
    }

    private function hasSttyAvailable()
    {
        if (null !== self::$stty) {
            return self::$stty;
        }

        exec('stty 2>&1', $output, $exitcode);

        return self::$stty = $exitcode === 0;
    }
}
