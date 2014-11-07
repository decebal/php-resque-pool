<?php

namespace Resque\Pool;

/**
 * Resque default logger PSR-3 compliant
 *
 */
class WorkLogger extends \Psr\Log\AbstractLogger 
{
	public $verbose;
	public $output;
	public function __construct($verbose = false, $output = false) {
		$this->verbose = $verbose;
		if ($output === false) {
			$this->output = STDOUT;
		} else {
			$this->output = fopen($output, 'a+');
		}
		
		$this->output = $output? $output : STDOUT;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed   $level    PSR-3 log level constant, or equivalent string
	 * @param string  $message  Message to log, may contain a { placeholder }
	 * @param array   $context  Variables to replace { placeholder }
	 * @return null
	 */
	public function log($level, $message, array $context = array())
	{
		if ($this->verbose) {
			fwrite(
				$this->output,
				'[' . $level . '] [' . strftime('%T %Y-%m-%d') . '] ' . $this->interpolate($message, $context) . PHP_EOL
			);
			return;
		}

		if (!($level === \Psr\Log\LogLevel::INFO || $level === \Psr\Log\LogLevel::DEBUG)) {
			fwrite(
				$this->output,
				'[' . $level . '] ' . $this->interpolate($message, $context) . PHP_EOL
			);
		}
	}

	/**
	 * Fill placeholders with the provided context
	 * @author Jordi Boggiano j.boggiano@seld.be
	 * 
	 * @param  string  $message  Message to be logged
	 * @param  array   $context  Array of variables to use in message
	 * @return string
	 */
	public function interpolate($message, array $context = array())
	{
		// build a replacement array with braces around the context keys
		$replace = array();
		foreach ($context as $key => $val) {
			$replace['{' . $key . '}'] = $val;
		}
	
		// interpolate replacement values into the message and return
		return strtr($message, $replace);
	}
}
