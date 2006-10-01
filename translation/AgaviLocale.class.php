<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2006 the Agavi Project.                                |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

/**
 * The locale saves all kind of info about a locale
 *
 * @package    agavi
 * @subpackage translation
 *
 * @author     Dominik del Bondio <ddb@bitxtender.com>
 * @author     Agavi Project <info@agavi.org>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
class AgaviLocale
{

	protected $context = null;

	/**
	 * @var        array The data
	 */
	protected $data = array();

	/**
	 * @var        string The name of this locale
	 */
	protected $name = null;


	public function initialize(AgaviContext $context, $name, array $data = array())
	{
		$this->context = $context;
		$this->name = $name;
		$this->data = $data;
	}

	public function getName()
	{
		return $this->name;
	}

	////////////////////////////// Locale data //////////////////////////////////

	public function getLocaleLanguage()
	{
		return isset($this->data['locale']['language'])
			? $this->data['locale']['language']
			: null;
	}

	public function getLocaleTerritory()
	{
		return isset($this->data['locale']['territory'])
			? $this->data['locale']['territory']
			: null;
	}

	public function getLocaleScript()
	{
		return isset($this->data['locale']['script'])
			? $this->data['locale']['script']
			: null;
	}

	public function getLocaleVariant()
	{
		return isset($this->data['locale']['variant'])
			? $this->data['locale']['variant']
			: null;
	}


	///////////////////////////// locale names //////////////////////////////////


	public function getLanguages()
	{
		return isset($this->data['displayNames']['languages'])
			? $this->data['displayNames']['languages']
			: null;
	}

	public function getLanguage($id)
	{
		return isset($this->data['displayNames']['languages'][$id])
			? $this->data['displayNames']['languages'][$id]
			: null;
	}


	public function getScripts()
	{
		return isset($this->data['displayNames']['scripts'])
			? $this->data['displayNames']['scripts']
			: null;
	}

	public function getScript($id)
	{
		return isset($this->data['displayNames']['scripts'][$id])
			? $this->data['displayNames']['scripts'][$id]
			: null;
	}


	public function getTerritories()
	{
		return isset($this->data['displayNames']['territories'])
			? $this->data['displayNames']['territories']
			: null;
	}

	public function getTerritory($id)
	{
		return isset($this->data['displayNames']['territories'][$id])
			? $this->data['displayNames']['territories'][$id]
			: null;
	}


	public function getVariants()
	{
		return isset($this->data['displayNames']['variants'])
			? $this->data['displayNames']['variants']
			: null;
	}

	public function getVariant($id)
	{
		return isset($this->data['displayNames']['variants'][$id])
			? $this->data['displayNames']['variants'][$id]
			: null;
	}


	public function getMeasurementSystemNames()
	{
		return isset($this->data['displayNames']['measurementSystemNames'])
			? $this->data['displayNames']['measurementSystemNames']
			: null;
	}

	public function getMeasurementSystemName($id)
	{
		return isset($this->data['displayNames']['measurementSystemNames'][$id])
			? $this->data['displayNames']['measurementSystemNames'][$id]
			: null;
	}


	//////////////////////////////// layout /////////////////////////////////////


	public function getLineOrientation()
	{
		return isset($this->data['layout']['orientation']['lines'])
			? $this->data['layout']['orientation']['lines']
			: null;
	}

	public function getCharacterOrientation()
	{
		return isset($this->data['layout']['orientation']['characters'])
			? $this->data['layout']['orientation']['characters']
			: null;
	}


	//////////////////////////////// delimiters /////////////////////////////////


	public function getQuotationStart()
	{
		return isset($this->data['delimiters']['quotationStart'])
			? $this->data['delimiters']['quotationStart']
			: null;
	}

	public function getQuotationEnd()
	{
		return isset($this->data['delimiters']['quotationEnd'])
			? $this->data['delimiters']['quotationEnd']
			: null;
	}

	public function getAlternateQuotationStart()
	{
		return isset($this->data['delimiters']['altQuotationStart'])
			? $this->data['delimiters']['altQuotationStart']
			: null;
	}

	public function getAlternateQuotationEnd()
	{
		return isset($this->data['delimiters']['altQuotationEnd'])
			? $this->data['delimiters']['altQuotationEnd']
			: null;
	}


	//////////////////////////////// calendars //////////////////////////////////


	public function getDefaultCalendar()
	{
		return isset($this->data['calendars']['default'])
			? $this->data['calendars']['default']
			: null;
	}

	public function getCalendarMonthsWide($calendar)
	{
		return isset($this->data['calendars'][$calendar]['months']['format']['wide'])
			? $this->data['calendars'][$calendar]['months']['format']['wide']
			: null;
	}

	public function getCalendarMonthWide($calendar, $month)
	{
		return isset($this->data['calendars'][$calendar]['months']['format']['wide'][$month])
			? $this->data['calendars'][$calendar]['months']['format']['wide'][$month]
			: null;
	}

	public function getCalendarMonthsAbbreviated($calendar)
	{
		return isset($this->data['calendars'][$calendar]['months']['format']['abbreviated'])
			? $this->data['calendars'][$calendar]['months']['format']['abbreviated']
			: null;
	}

	public function getCalendarMonthAbbreviated($calendar, $month)
	{
		return isset($this->data['calendars'][$calendar]['months']['format']['abbreviated'][$month])
			? $this->data['calendars'][$calendar]['months']['format']['abbreviated'][$month]
			: null;
	}

	public function getCalendarMonthsNarrow($calendar)
	{
		return isset($this->data['calendars'][$calendar]['months']['stand-alone']['narrow'])
			? $this->data['calendars'][$calendar]['months']['stand-alone']['narrow']
			: null;
	}

	public function getCalendarMonthNarrow($calendar, $month)
	{
		return isset($this->data['calendars'][$calendar]['months']['stand-alone']['narrow'][$month])
			? $this->data['calendars'][$calendar]['months']['stand-alone']['narrow'][$month]
			: null;
	}


	public function getCalendarDaysWide($calendar)
	{
		return isset($this->data['calendars'][$calendar]['days']['format']['wide'])
			? $this->data['calendars'][$calendar]['days']['format']['wide']
			: null;
	}

	public function getCalendarDayWide($calendar, $day)
	{
		return isset($this->data['calendars'][$calendar]['days']['format']['wide'][$day])
			? $this->data['calendars'][$calendar]['days']['format']['wide'][$day]
			: null;
	}

	public function getCalendarDaysAbbreviated($calendar)
	{
		return isset($this->data['calendars'][$calendar]['days']['format']['abbreviated'])
			? $this->data['calendars'][$calendar]['days']['format']['abbreviated']
			: null;
	}

	public function getCalendarDayAbbreviated($calendar, $day)
	{
		return isset($this->data['calendars'][$calendar]['days']['format']['abbreviated'][$day])
			? $this->data['calendars'][$calendar]['days']['format']['abbreviated'][$day]
			: null;
	}

	public function getCalendarDaysNarrow($calendar)
	{
		return isset($this->data['calendars'][$calendar]['days']['stand-alone']['narrow'])
			? $this->data['calendars'][$calendar]['days']['stand-alone']['narrow']
			: null;
	}

	public function getCalendarDayNarrow($calendar, $day)
	{
		return isset($this->data['calendars'][$calendar]['days']['stand-alone']['narrow'][$day])
			? $this->data['calendars'][$calendar]['days']['stand-alone']['narrow'][$day]
			: null;
	}


	public function getCalendarQuartersWide($calendar)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['format']['wide'])
			? $this->data['calendars'][$calendar]['quarters']['format']['wide']
			: null;
	}

	public function getCalendarQuarterWide($calendar, $quarter)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['format']['wide'][$quarter])
			? $this->data['calendars'][$calendar]['quarters']['format']['wide'][$quarter]
			: null;
	}

	public function getCalendarQuartersAbbreviated($calendar)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['format']['abbreviated'])
			? $this->data['calendars'][$calendar]['quarters']['format']['abbreviated']
			: null;
	}

	public function getCalendarQuarterAbbreviated($calendar, $quater)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['format']['abbreviated'][$quarter])
			? $this->data['calendars'][$calendar]['quarters']['format']['abbreviated'][$quarter]
			: null;
	}

	public function getCalendarQuartersNarrow($calendar)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['stand-alone']['narrow'])
			? $this->data['calendars'][$calendar]['quarters']['stand-alone']['narrow']
			: null;
	}

	public function getCalendarQuarterNarrow($calendar, $quarter)
	{
		return isset($this->data['calendars'][$calendar]['quarters']['stand-alone']['narrow'][$quarter])
			? $this->data['calendars'][$calendar]['quarters']['stand-alone']['narrow'][$quarter]
			: null;
	}


	public function getCalendarAm($calendar)
	{
		return isset($this->data['calendars'][$calendar]['am'])
			? $this->data['calendars'][$calendar]['am']
			: null;
	}

	public function getCalendarPm($calendar)
	{
		return isset($this->data['calendars'][$calendar]['pm'])
			? $this->data['calendars'][$calendar]['pm']
			: null;
	}


	public function getCalendarErasWide($calendar)
	{
		return isset($this->data['calendars'][$calendar]['eras']['wide'])
			? $this->data['calendars'][$calendar]['eras']['wide']
			: null;
	}

	public function getCalendarEraWide($calendar, $era)
	{
		return isset($this->data['calendars'][$calendar]['eras']['wide'][$era])
			? $this->data['calendars'][$calendar]['eras']['wide'][$era]
			: null;
	}

	public function getCalendarErasAbbreviated($calendar)
	{
		return isset($this->data['calendars'][$calendar]['eras']['abbreviated'])
			? $this->data['calendars'][$calendar]['eras']['abbreviated']
			: null;
	}

	public function getCalendarEraAbbreviated($calendar, $era)
	{
		return isset($this->data['calendars'][$calendar]['eras']['abbreviated'][$era])
			? $this->data['calendars'][$calendar]['eras']['abbreviated'][$era]
			: null;
	}

	public function getCalendarErasNarrow($calendar)
	{
		return isset($this->data['calendars'][$calendar]['eras']['narrow'])
			? $this->data['calendars'][$calendar]['eras']['narrow']
			: null;
	}

	public function getCalendarEraNarrow($calendar, $era)
	{
		return isset($this->data['calendars'][$calendar]['eras']['narrow'][$era])
			? $this->data['calendars'][$calendar]['eras']['narrow'][$era]
			: null;
	}

	public function getCalendarDateFormatDefaultName($calendar)
	{
		return isset($this->data['calendars'][$calendar]['dateFormats']['default'])
			? $this->data['calendars'][$calendar]['dateFormats']['default']
			: null;
	}

	public function getCalendarDateFormats($calendar)
	{
		return isset($this->data['calendars'][$calendar]['dateFormats'])
			? $this->data['calendars'][$calendar]['dateFormats']
			: null;
	}

	public function getCalendarDateFormat($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateFormats'][$id])
			? $this->data['calendars'][$calendar]['dateFormats'][$id]
			: null;
	}

	public function getCalendarDateFormatPattern($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateFormats'][$id]['pattern'])
			? $this->data['calendars'][$calendar]['dateFormats'][$id]['pattern']
			: null;
	}

	public function getCalendarDateFormatDisplayName($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateFormats'][$id]['displayName'])
			? $this->data['calendars'][$calendar]['dateFormats'][$id]['displayName']
			: null;
	}

	public function getCalendarTimeFormatDefaultName($calendar)
	{
		return isset($this->data['calendars'][$calendar]['timeFormats']['default'])
			? $this->data['calendars'][$calendar]['timeFormats']['default']
			: null;
	}

	public function getCalendarTimeFormats($calendar)
	{
		return isset($this->data['calendars'][$calendar]['timeFormats'])
			? $this->data['calendars'][$calendar]['timeFormats']
			: null;
	}

	public function getCalendarTimeFormat($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['timeFormats'][$id])
			? $this->data['calendars'][$calendar]['timeFormats'][$id]
			: null;
	}

	public function getCalendarTimeFormatPattern($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['timeFormats'][$id]['pattern'])
			? $this->data['calendars'][$calendar]['timeFormats'][$id]['pattern']
			: null;
	}

	public function getCalendarTimeFormatDisplayName($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['timeFormats'][$id]['displayName'])
			? $this->data['calendars'][$calendar]['timeFormats'][$id]['displayName']
			: null;
	}

	public function getCalendarDateTimeFormatDefaultName($calendar)
	{
		return isset($this->data['calendars'][$calendar]['dateTimeFormats']['default'])
			? $this->data['calendars'][$calendar]['dateTimeFormats']['default']
			: null;
	}

	public function getCalendarDateTimeFormats($calendar)
	{
		return isset($this->data['calendars'][$calendar]['dateTimeFormats'])
			? $this->data['calendars'][$calendar]['dateTimeFormats']
			: null;
	}

	public function getCalendarDateTimeFormat($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateTimeFormats'][$id])
			? $this->data['calendars'][$calendar]['dateTimeFormats'][$id]
			: null;
	}

	public function getCalendarDateTimeFormatPattern($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateTimeFormats'][$id]['pattern'])
			? $this->data['calendars'][$calendar]['dateTimeFormats'][$id]['pattern']
			: null;
	}

	public function getCalendarDateTimeFormatDisplayName($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['dateTimeFormats'][$id]['displayName'])
			? $this->data['calendars'][$calendar]['dateTimeFormats'][$id]['displayName']
			: null;
	}


	public function getCalendarFields($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['fields'])
			? $this->data['calendars'][$calendar]['fields']
			: null;
	}

	public function getCalendarField($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['fields'][$id])
			? $this->data['calendars'][$calendar]['fields'][$id]
			: null;
	}

	public function getCalendarFieldDisplayName($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['fields'][$id]['displayName'])
			? $this->data['calendars'][$calendar]['fields'][$id]['displayName']
			: null;
	}

	public function getCalendarFieldRelatives($calendar, $id)
	{
		return isset($this->data['calendars'][$calendar]['fields'][$id]['relatives'])
			? $this->data['calendars'][$calendar]['fields'][$id]['relatives']
			: null;
	}

	public function getCalendarFieldRelative($calendar, $id, $rId)
	{
		return isset($this->data['calendars'][$calendar]['fields'][$id]['relatives'][$rId])
			? $this->data['calendars'][$calendar]['fields'][$id]['relatives'][$rId]
			: null;
	}


	public function getTimeZoneHourFormat()
	{
		return isset($this->data['timeZoneNames']['hourFormat'])
			? $this->data['timeZoneNames']['hourFormat']
			: null;
	}

	public function getTimeZoneHoursFormat()
	{
		return isset($this->data['timeZoneNames']['hoursFormat'])
			? $this->data['timeZoneNames']['hoursFormat']
			: null;
	}

	public function getTimeZoneGmtFormat()
	{
		return isset($this->data['timeZoneNames']['gmtFormat'])
			? $this->data['timeZoneNames']['gmtFormat']
			: null;
	}

	public function getTimeZoneRegionFormat()
	{
		return isset($this->data['timeZoneNames']['regionFormat'])
			? $this->data['timeZoneNames']['regionFormat']
			: null;
	}

	public function getTimeZoneFallbackFormat()
	{
		return isset($this->data['timeZoneNames']['fallbackFormat'])
			? $this->data['timeZoneNames']['fallbackFormat']
			: null;
	}

	public function getTimeZoneAbbreviationFormat()
	{
		return isset($this->data['timeZoneNames']['abbreviationFormat'])
			? $this->data['timeZoneNames']['abbreviationFormat']
			: null;
	}

	public function getTimeZoneLongGenericName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['long']['generic'])
			? $this->data['timeZoneNames']['zones'][$tz]['long']['generic']
			: null;
	}

	public function getTimeZoneLongStandardName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['long']['standard'])
			? $this->data['timeZoneNames']['zones'][$tz]['long']['standard']
			: null;
	}

	public function getTimeZoneLongDaylightName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['long']['daylight'])
			? $this->data['timeZoneNames']['zones'][$tz]['long']['daylight']
			: null;
	}

	public function getTimeZoneShortGenericName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['short']['generic'])
			? $this->data['timeZoneNames']['zones'][$tz]['short']['generic']
			: null;
	}

	public function getTimeZoneShortStandardName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['short']['standard'])
			? $this->data['timeZoneNames']['zones'][$tz]['short']['standard']
			: null;
	}

	public function getTimeZoneShortDaylightName($tz)
	{
		return isset($this->data['timeZoneNames']['zones'][$tz]['short']['daylight'])
			? $this->data['timeZoneNames']['zones'][$tz]['short']['daylight']
			: null;
	}


	public function getNumberSymbolDecimal()
	{
		return isset($this->data['numbers']['symbols']['decimal'])
			? $this->data['numbers']['symbols']['decimal']
			: null;
	}

	public function getNumberSymbolGroup()
	{
		return isset($this->data['numbers']['symbols']['group'])
			? $this->data['numbers']['symbols']['group']
			: null;
	}

	public function getNumberSymbolList()
	{
		return isset($this->data['numbers']['symbols']['list'])
			? $this->data['numbers']['symbols']['list']
			: null;
	}

	public function getNumberSymbolPercentSign()
	{
		return isset($this->data['numbers']['symbols']['percentSign'])
			? $this->data['numbers']['symbols']['percentSign']
			: null;
	}

	public function getNumberSymbolZeroDigit()
	{
		return isset($this->data['numbers']['symbols']['nativeZeroDigit'])
			? $this->data['numbers']['symbols']['nativeZeroDigit']
			: null;
	}

	public function getNumberSymbolPatternDigit()
	{
		return isset($this->data['numbers']['symbols']['patternDigit'])
			? $this->data['numbers']['symbols']['patternDigit']
			: null;
	}

	public function getNumberSymbolPlusSign()
	{
		return isset($this->data['numbers']['symbols']['plusSign'])
			? $this->data['numbers']['symbols']['plusSign']
			: null;
	}

	public function getNumberSymbolMinusSign()
	{
		return isset($this->data['numbers']['symbols']['minusSign'])
			? $this->data['numbers']['symbols']['minusSign']
			: null;
	}

	public function getNumberSymbolExponential()
	{
		return isset($this->data['numbers']['symbols']['exponential'])
			? $this->data['numbers']['symbols']['exponential']
			: null;
	}

	public function getNumberSymbolPerMille()
	{
		return isset($this->data['numbers']['symbols']['perMille'])
			? $this->data['numbers']['symbols']['perMille']
			: null;
	}

	public function getNumberSymbolInfinity()
	{
		return isset($this->data['numbers']['symbols']['infinity'])
			? $this->data['numbers']['symbols']['infinity']
			: null;
	}

	public function getNumberSymbolNaN()
	{
		return isset($this->data['numbers']['symbols']['nan'])
			? $this->data['numbers']['symbols']['nan']
			: null;
	}


	public function getDecimalFormat($dfId)
	{
		return isset($this->data['numbers']['decimalFormats'][$dfId])
			? $this->data['numbers']['decimalFormats'][$dfId]
			: null;
	}

	public function getDecimalFormats()
	{
		return isset($this->data['numbers']['decimalFormats'])
			? $this->data['numbers']['decimalFormats']
			: null;
	}

	public function getScientificFormat($sfId)
	{
		return isset($this->data['numbers']['scientificFormats'][$sfId])
			? $this->data['numbers']['scientificFormats'][$sfId]
			: null;
	}

	public function getScientificFormats()
	{
		return isset($this->data['numbers']['scientificFormats'])
			? $this->data['numbers']['scientificFormats']
			: null;
	}

	public function getPercentFormat($dfId)
	{
		return isset($this->data['numbers']['percentFormats'][$pfId])
			? $this->data['numbers']['percentFormats'][$pfId]
			: null;
	}

	public function getPercentFormats()
	{
		return isset($this->data['numbers']['percentFormats'])
			? $this->data['numbers']['percentFormats']
			: null;
	}

	public function getCurrencyFormat($cfId)
	{
		return isset($this->data['numbers']['currencyFormats'][$cfId])
			? $this->data['numbers']['currencyFormats'][$cfId]
			: null;
	}

	public function getCurrencyFormats()
	{
		return isset($this->data['numbers']['currencyFormats'])
			? $this->data['numbers']['currencyFormats']
			: null;
	}


	public function getCurrencySpacingBeforeCurrencyCurrencyMatch()
	{
		return isset($this->data['numbers']['currencySpacing']['beforeCurrency']['currencyMatch'])
			? $this->data['numbers']['currencySpacing']['beforeCurrency']['currencyMatch']
			: null;
	}

	public function getCurrencySpacingBeforeCurrencySurroundingMatch()
	{
		return isset($this->data['numbers']['currencySpacing']['beforeCurrency']['surroundingMatch'])
			? $this->data['numbers']['currencySpacing']['beforeCurrency']['surroundingMatch']
			: null;
	}

	public function getCurrencySpacingBeforeCurrencyInsertBetween()
	{
		return isset($this->data['numbers']['currencySpacing']['beforeCurrency']['insertBetween'])
			? $this->data['numbers']['currencySpacing']['beforeCurrency']['insertBetween']
			: null;
	}

	public function getCurrencySpacingAfterCurrencyCurrencyMatch()
	{
		return isset($this->data['numbers']['currencySpacing']['afterCurrency']['currencyMatch'])
			? $this->data['numbers']['currencySpacing']['afterCurrency']['currencyMatch']
			: null;
	}

	public function getCurrencySpacingAfterCurrencySurroundingMatch()
	{
		return isset($this->data['numbers']['currencySpacing']['afterCurrency']['surroundingMatch'])
			? $this->data['numbers']['currencySpacing']['afterCurrency']['surroundingMatch']
			: null;
	}

	public function getCurrencySpacingAfterCurrencyInsertBetween()
	{
		return isset($this->data['numbers']['currencySpacing']['afterCurrency']['insertBetween'])
			? $this->data['numbers']['currencySpacing']['afterCurrency']['insertBetween']
			: null;
	}


	public function getCurrencies()
	{
		return isset($this->data['numbers']['currencies'])
			? $this->data['numbers']['currencies']
			: null;
	}

	public function getCurrency($cId)
	{
		return isset($this->data['numbers']['currencies'][$cId])
			? $this->data['numbers']['currencies'][$cId]
			: null;
	}

	public function getCurrencyDisplayName($cId)
	{
		return isset($this->data['numbers']['currencies'][$cId]['displayName'])
			? $this->data['numbers']['currencies'][$cId]['displayName']
			: null;
	}

	public function getCurrencySymbol($cId)
	{
		return isset($this->data['numbers']['currencies'][$cId]['symbol'])
			? $this->data['numbers']['currencies'][$cId]['symbol']
			: null;
	}


	public static function parseLocaleIdentifier($identifier)
	{
		// the only important thing here is the forward assertion which is needed
		// so it doesn't match the first character of the territory
		$baseLocaleRx = '(?P<language>[^_@]{2,3})(?:_(?P<script>[^_@](?=@|_|$)|[^_@]{4,}))?(?:_(?P<territory>[^_@]{2,3}))?(?:_(?P<variant>[^@]+))?';
		$optionsRx = '@(?P<options>.*)';

		$localeRx = '#^(' . $baseLocaleRx . ')(' . $optionsRx . ')?$#';

		$localeData = array(
			'language' => null,
			'script' => null,
			'territory' => null,
			'variant' => null,
			'options' => array()
		);

		if(preg_match($localeRx, $identifier, $match)) {
			$localeData['language'] = $match['language'];
			if(!empty($match['script'])) {
				$localeData['script'] = $match['script'];
			}
			if(!empty($match['territory'])) {
				$localeData['territory'] = $match['territory'];
			}
			if(!empty($match['variant'])) {
				$localeData['variant'] = $match['variant'];
			}

			if(!empty($match['options'])) {
				$options = explode(',', $match['options']);
				foreach($options as $option) {
					$optData = explode('=', $option, 2);
					$localeData['options'][$optData[0]] = $optData[1];
				}
			}
		} else {
			throw new AgaviException('Invalid locale identifier (' . $identifier . ') specified');
		}

		return $localeData;
	}

	public static function getLookupPath($localeIdentifier)
	{
		$localeInfo = self::parseLocaleIdentifier($localeIdentifier);

		
		$path = $localeInfo['language'];
		$paths[] = $path;

		if($localeInfo['script']) {
			$path .= '_' . $localeInfo['script'];
			$paths[] = $path;
		}

		if($localeInfo['territory']) {
			$path .= '_' . $localeInfo['territory'];
			$paths[] = $path;
		}

		if($localeInfo['variant']) {
			$path .= '_' . $localeInfo['variant'];
			$paths[] = $path;
		}

		return array_reverse($paths);
	}
}

?>