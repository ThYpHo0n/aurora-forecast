<?

require "vendor/autoload.php";

class App {

	const FORECAST_IMG_URL = "http://services.swpc.noaa.gov/images/aurora-forecast-northern-hemisphere.png";
	const IFTTT_TRIGGER_URL = "https://maker.ifttt.com/trigger/aurora_forecast/with/key/someKey";
	const ALERT_IMG_ARCHIVE = "var/archive/";

	// Approximately northern germany/hamburg
	const MY_CORDS_X = 621;
	const MY_CORDS_Y = 318;

	/**
	 * This is the default color of the pixel
	 * @param $red
	 * @param $green
	 * @param $blue
	 *
	 * @return bool
	 */
	private static function checkDefault($red, $green, $blue) {
		if($red == 51 && $green == 86 && $blue == 22) {
			return true;
		}

		return false;
	}

	/**
	 * If the red line lays on the pixel
	 * @param $red
	 *
	 * @return bool
	 */
	private static function checkRedLine($red) {
		if($red > 110) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the aurora forecasts casts a bright green overlay over the pixel
	 * @param $red
	 * @param $green
	 * @param $blue
	 *
	 * @return bool
	 */
	private static function checkAurora($red, $green, $blue) {
		// Do we have bright green in our pixel?
		if(($red > 23 && $red < 36) && ($green > 97 && $green < 155) && ($blue > 0 && $blue < 18) ) {
			return true;
		}

		// If auroras are high likely then the overlay is yellowish
		if(($red > 128 && $red < 219) && ($green > 224 && $green <= 255) && ($blue < 5)) {
			return true;
		}

		return false;
	}

	/**
	 * Main application logic
	 */
	public static function run() {
		try {

			$httpClient = new GuzzleHttp\Client();
			$date = date('Y-m-d-h-i-s', time());

			$response = $httpClient->get(self::FORECAST_IMG_URL);
			$sImg = $response->getBody()->getContents();
			$img = imagecreatefromstring($sImg);

			$color = imagecolorat($img, self::MY_CORDS_X, self::MY_CORDS_Y);
			$rgb = imagecolorsforindex($img, $color);
			$red = $rgb['red'];
			$green = $rgb['green'];
			$blue = $rgb['blue'];

			if(!self::checkDefault($red, $green, $blue) && (self::checkRedLine($red) || self::checkAurora($red, $green, $blue))) {
				// Alert! We have a positive aurora forecast
				file_put_contents(sprintf("%s%s.img", self::ALERT_IMG_ARCHIVE, $date), $sImg);
				$ifHttpClient = new GuzzleHttp\Client();
				$ifHttpClient->get(self::IFTTT_TRIGGER_URL);
			}

			exit(0);
		} catch (\Exception $e) {
			var_dump($e);
			exit(1);
		}
	}
}

date_default_timezone_set("Europe/Berlin");
App::run();
