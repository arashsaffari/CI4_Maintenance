<?php namespace CodeigniterExt\MaintenanceMode\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Down extends BaseCommand
{
	protected $group        = 'Maintenance Mode';
	protected $name         = 'mm:down';
	protected $description  = 'Put the application into maintenance mode';
	protected $usage        = 'mm:down';
	protected $arguments    = [];
	protected $options 		= [];

	public function run(array $params)
	{
		$config = config( 'CodeigniterExt\\MaintenanceMode\\MaintenanceMode' );

		if (! file_exists($config->FilePath . $config->FileName)) {
			
			$message = CLI::prompt("Message");
			$ips_str = CLI::prompt("Allowed ips [example: 0.0.0.0 127.0.0.1]");

			$ips_array = explode(" ", $ips_str);

			//
			// dir doesn't exist, make it
			//
			if (!is_dir($config->FilePath)) {
				mkdir($config->FilePath);
			}

			//
			// write the file with json content
			//
			file_put_contents(
				$config->FilePath . $config->FileName,
				json_encode([
					"time"			=> strtotime("now"),
					"message" 		=> $message,
					"cookie_name"	=> $this->randomhash(8),
					"allowed_ips"	=> $ips_array
				], JSON_PRETTY_PRINT)
			);

			CLI::write('');
			CLI::write('**** Application is now DOWN. ****', 'red');
			CLI::write('');

			$this->call('mm:status');

		}else{
			CLI::write('');
			CLI::write('**** Application is already DOWN. ****', 'red');
			CLI::write('');
		}
	}

	function randomhash($len = 8){
		$seed = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
		shuffle($seed);
		$rand = '';
		
		foreach (array_rand($seed, $len) as $k)
		{
			$rand .= $seed[$k];
		}
		
		return $rand;
	  }
}
