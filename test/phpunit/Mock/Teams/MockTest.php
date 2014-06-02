<?php
/**
 * @link      https://github.com/gridiron-guru/FantasyDataAPI for the canonical source repository
 * @copyright Copyright (c) 2014 Robert Gunnar Johnson Jr.
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package   FantasyDataAPI
 */

namespace FantasyDataAPI\Test\Mock\Teams;

use FantasyDataAPI\Enum\Subscription;
use PHPUnit_Framework_TestCase;

use FantasyDataAPI\Test\Mock\Client;

use FantasyDataAPI\Enum\Teams;
use FantasyDataAPI\Enum\Stadium;

class MockTest extends PHPUnit_Framework_TestCase
{
    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect that the api key is placed in the URL as expected by the service
     *
     * Expect a service URL something like this:
     *   http://api.nfldata.apiphany.com/developer/json/Teams/2014?key=000aaaa0-a00a-0000-0a0a-aa0a00000000
     */
    public function testAPIKeyParameter()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);
//         $client = new \FantasyDataAPI\Test\DebugClient($_SERVER['FANTASY_DATA_API_KEY'], 'developer');

        /** \GuzzleHttp\Command\Model */
        $client->Teams(['Season' => '2014']);

        $response = $client->mHistory->getLastResponse();
        $effective_url = $response->getEffectiveUrl();

        $matches = [];

        /**
         * not the most elegant way to test for the query parameter, but it's not real easy
         * to get at them with the method i'm using. Not sure if there's a better method or
         * not. If you happen to look at this and know a better way to get query params etc.
         * from Guzzle, let me know.
         */
        $pattern = '/key=' . $_SERVER['FANTASY_DATA_API_KEY'] . '/';
        preg_match($pattern, $effective_url, $matches);

        $this->assertNotEmpty($matches);
    }

    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect that the proper subscription type is placed in the URI
     */
    public function testSubscriptionInURI()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);

        /** \GuzzleHttp\Command\Model */
        $client->Teams(['Season' => '2014']);

        $response = $client->mHistory->getLastResponse();
        $effective_url = $response->getEffectiveUrl();

        $pieces = explode('/', $effective_url);

        /** key 3 should be the "subscription type" based on URL structure */
        $this->assertArrayHasKey(3, $pieces);
        $this->assertEquals( $pieces[3], Subscription::KEY_DEVELOPER);
    }

    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect that the json format is placed in the URI
     */
    public function testFormatInURI()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);

        /** \GuzzleHttp\Command\Model */
        $client->Teams(['Season' => '2014']);

        $response = $client->mHistory->getLastResponse();
        $effective_url = $response->getEffectiveUrl();

        $pieces = explode('/', $effective_url);

        /** key 4 should be the "format" based on URL structure */
        $this->assertArrayHasKey(4, $pieces);
        $this->assertEquals( $pieces[4], 'json');
    }

    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect that the Timeframe resource is placed in the URI
     */
    public function testResourceInURI()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);

        /** \GuzzleHttp\Command\Model */
        $client->Teams(['Season' => '2014']);

        $response = $client->mHistory->getLastResponse();
        $effective_url = $response->getEffectiveUrl();

        $pieces = explode('/', $effective_url);

        /** key 5 should be the "resource" based on URL structure */
        $this->assertArrayHasKey(5, $pieces);
        $this->assertEquals( $pieces[5], 'Teams');
    }

    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect a 200 response with an array of teams, each containing a stadium
     */
    public function test2014TeamsSuccessfulResponse()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);

        /** @var \GuzzleHttp\Command\Model $result */
        $result = $client->Teams(['Season' => '2014']);

        $response = $client->mHistory->getLastResponse();

        $this->assertEquals('200', $response->getStatusCode());

        /** we expect 32 teams for 2014 */
        $this->assertCount( 32, $result );

        $check_team_keys = function ( $pTeam )
        {
            /** we expect 8 keys */
            $this->assertCount( 8, $pTeam );

            /** test all the keys */
            $this->assertArrayHasKey( Teams\Property::KEY_CITY, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_CONFERENCE, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_DIVISION, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_FULL_NAME, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_KEY, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_NAME, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_STADIUM_DETAILS, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_STADIUM_ID, $pTeam );

            /** we expect 7 keys */
            $this->assertCount( 7, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );

            /** test all the properties */
            $this->assertArrayHasKey( Stadium\Property::KEY_CAPACITY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_CITY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_COUNTRY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_NAME, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_PLAYING_SURFACE, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_STADIUM_ID, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_STATE, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
        };

        $teams = $result->toArray();

        array_walk( $teams, $check_team_keys );
    }

    /**
     * Given: A developer API key
     * When: API is queried for 2014 Teams
     * Then: Expect a 200 response with an array of teams, each containing a stadium
     */
    public function test2013TeamsSuccessfulResponse()
    {
        $client = new Client($_SERVER['FANTASY_DATA_API_KEY'], Subscription::KEY_DEVELOPER);

        /** @var \GuzzleHttp\Command\Model $result */
        $result = $client->Teams(['Season' => '2013']);

        $response = $client->mHistory->getLastResponse();

        $this->assertEquals('200', $response->getStatusCode());

        /** we expect 32 teams for 2013 */
        $this->assertCount( 32, $result );

        $check_team_keys = function ( $pTeam )
        {
            /** we expect 8 keys */
            $this->assertCount( 8, $pTeam );

            /** test all the keys */
            $this->assertArrayHasKey( Teams\Property::KEY_CITY, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_CONFERENCE, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_DIVISION, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_FULL_NAME, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_KEY, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_NAME, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_STADIUM_DETAILS, $pTeam );
            $this->assertArrayHasKey( Teams\Property::KEY_STADIUM_ID, $pTeam );

            /** we expect 7 keys */
            $this->assertCount( 7, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );

            /** test all the properties */
            $this->assertArrayHasKey( Stadium\Property::KEY_CAPACITY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_CITY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_COUNTRY, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_NAME, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_PLAYING_SURFACE, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_STADIUM_ID, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
            $this->assertArrayHasKey( Stadium\Property::KEY_STATE, $pTeam[Teams\Property::KEY_STADIUM_DETAILS] );
        };

        $teams = $result->toArray();

        array_walk( $teams, $check_team_keys );
    }
}
