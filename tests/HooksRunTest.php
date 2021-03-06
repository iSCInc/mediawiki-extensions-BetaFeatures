<?php
/*
 * This file is part of the MediaWiki extension BetaFeatures.
 *
 * BetaFeatures is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * BetaFeatures is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BetaFeatures.  If not, see <http://www.gnu.org/licenses/>.
 *
 * BetaFeatures hooks tests
 *
 * @file
 * @ingroup Extensions
 * @copyright 2013 Mark Holmquist and others; see AUTHORS
 * @license GNU General Public License version 2 or later
 */

class HooksRunTest extends MediaWikiTestCase {

	// Key for testing preference
	const testPrefKey = 'unittest';

	// Structure of testing preference
	static $testPref = array(
		'label-message' => 'nullish',
		'desc-message' => 'nullish',
		'info-link' => 'https://mediawiki.org/wiki/Extension:BetaFeatures',
		'discussion-link' => 'https://mediawiki.org/wiki/Extension_talk:BetaFeatures',
	);

	static function nullHook( $user, &$betaPrefs ) {
		return true;
	}

	static function hookThatRegistersPreference( $user, &$betaPrefs ) {
		$betaPrefs[self::testPrefKey] = self::$testPref;
		return true;
	}

	protected function setUp() {
		parent::setUp();

		$this->user = new User;
		$this->user->addGroup( 'unittesters' );
	}

	public function testCanRegisterHooks() {
		global $wgHooks;
		$this->assertArrayHasKey( 'GetBetaFeaturePreferences', $wgHooks, 'Hook array does not exist...' );
		$wgHooks['GetBetaFeaturePreferences'] = array( 'HooksRunTest::nullHook' );
		// There's no reason for this to not work
		$this->assertGreaterThan( 0, count( $wgHooks['GetBetaFeaturePreferences'] ), 'Hook did not get registered.' );
	}

	public function testHooksRun() {
		global $wgHooks;

		$wgHooks['GetBetaFeaturePreferences'] = array( 'HooksRunTest::hookThatRegistersPreference' );
		$prefs = array();
		wfRunHooks( 'GetBetaFeaturePreferences', array( $this->user, &$prefs ) );
		$this->assertArrayHasKey( self::testPrefKey, $prefs, 'Hook did not run' );
		$this->assertEquals( $prefs[self::testPrefKey], self::$testPref, 'The returned preference was not the same as what we registered.' );
	}
}
