<?php

namespace Geocoder\Tests\Provider;

use Geocoder\Tests\TestCase;
use Geocoder\Provider\GoogleMapsBusinessProvider;

class GoogleMapsBusinessProviderTest extends TestCase
{
    private $testClientId = 'foo';
    private $testPrivateKey = 'bogus';

    public function testGetName()
    {
        $provider = new GoogleMapsBusinessProvider($this->getMockAdapter($this->never()), $this->testClientId);
        $this->assertEquals('google_maps_business', $provider->getName());
    }

    public function testBuildQueryWithNoPrivateKey()
    {
        $method = new \ReflectionMethod(
          'Geocoder\Provider\GoogleMapsBusinessProvider', 'buildQuery'
        );

        $method->setAccessible(true);

        $provider = new GoogleMapsBusinessProvider($this->getMockAdapter($this->never()), $this->testClientId);

        $query = 'http://maps.googleapis.com/maps/api/geocode/json?address=blah&sensor=false';

        $this->assertEquals($query.'&client=foo', $method->invoke($provider, $query));
    }

    public function testBuildQueryWithPrivateKey()
    {
        $method = new \ReflectionMethod(
          'Geocoder\Provider\GoogleMapsBusinessProvider', 'buildQuery'
        );

        $method->setAccessible(true);

        $provider = new GoogleMapsBusinessProvider(
            $this->getMockAdapter($this->never()),
            $this->testClientId,
            $this->testPrivateKey
        );

        $query = 'http://maps.googleapis.com/maps/api/geocode/json?address=blah&sensor=false';

        $this->assertEquals($query.'&client=foo&signature=JY4upbd7fi76C-bMGYk410gmB5g=', $method->invoke($provider, $query));
    }

    public function testSignQuery()
    {
        $method = new \ReflectionMethod(
          'Geocoder\Provider\GoogleMapsBusinessProvider', 'signQuery'
        );

        $method->setAccessible(true);

        $provider = new GoogleMapsBusinessProvider(
            $this->getMockAdapter($this->never()),
            $this->testClientId,
            $this->testPrivateKey
        );

        $query = 'http://maps.googleapis.com/maps/api/geocode/json?address=10%20avenue%20Gambetta%2C%20Paris%2C%20France&sensor=false';

        $this->assertEquals($query.'&signature=yd07DufNspPyDE-Vj6nTeI5Fk-o=', $method->invoke($provider, $query));
    }

    /**
     * @expectedException Geocoder\Exception\InvalidCredentialsException
     * @expectedExceptionMessage Invalid client ID / API Key https://maps.googleapis.com/maps/api/geocode/json?address=Columbia%20University&sensor=false&client=foo&signature=xhvnqoQoLos6iqijCu3b1Odmqr0=
     */
    public function testGetGeocodedDataWithInvalidClientIdAndKey()
    {
        $provider = new GoogleMapsBusinessProvider($this->getAdapter(), $this->testClientId, $this->testPrivateKey, null, null, true);

        $provider->getGeocodedData('Columbia University');
    }

    /**
     * @expectedException Geocoder\Exception\InvalidCredentialsException
     * @expectedExceptionMessage Invalid client ID / API Key http://maps.googleapis.com/maps/api/geocode/json?address=Columbia%20University&sensor=false&client=foo&signature=xhvnqoQoLos6iqijCu3b1Odmqr0=
     */
    public function testGetGeocodedDataWithINvalidClientIdAndKeyNoSsl()
    {
        $provider = new GoogleMapsBusinessProvider($this->getAdapter(), $this->testClientId, $this->testPrivateKey, null, null, false);

        $provider->getGeocodedData('Columbia University', true);
    }
}
