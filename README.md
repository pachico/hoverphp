# HoverPHP

[![Author](https://img.shields.io/badge/author-@pachico-blue.svg)](https://github.com/pachico)
[![Source Code](https://img.shields.io/badge/source-pachico/hoverphp-blue.svg)](https://github.com/pachico/hoverphp)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/pachico/hoverphp/blob/main/LICENSE.md)
[![ci](https://github.com/pachico/hoverphp/actions/workflows/01-build.yml/badge.svg)](https://github.com/pachico/hoverphp/actions)
![php 7.4+](https://img.shields.io/badge/php-min%207.4-red.svg)

Small PHP library to interact with [Hoverfly](https://hoverfly.io/).

**Note**: this library is in very early stages and its API can be subject to change.

## Motivation

Docker has provided a way to easily craft integration tests. However, it won't always solve your problems.
Let's say you want to do integration test against AWS SDK, or against a Prometheus instance that already contains
a lot of data points, or against an application of yours that can easily be used in a container.
In these scenarios, Docker images won't do the trick, and this is where [Hoverfly](https://hoverfly.io/) shines, by
allowing you to have an easy way to capture and then simulate HTTP responses acting as both proxy and webserver.

However, although it's `hoverctl` cli app is easy to use, it's not integrated in the PHP ecosystem and, therefore,
forces you to do some cumbersome orchestration to put your tests alongside your simulation definitions.

Luckily, it has an awesome [REST API](https://docs.hoverfly.io/en/latest/pages/reference/api/api.html).
HoverPHP is a tiny SDK to manage that REST API from within your test cases.

For convenience, it is able to define simulations by using classes that implement
[PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/).

At the moment, it only handles those features related to setting up simulations, which was the main motivation for this
library. However, it can be easily extended if required. Feel free to fill an issue for that.

## Install

Via Composer

``` bash
composer require pachico/hoverphp
```

## Usage

Refer to the [examples/folder](/examples);

### Simple use case

Your integration test wants to make sure that your application `SuperApp` successfully communicates with service `SuperService`.

This is what a test case could look like:

```php

class MyAwesomeIntegrationTest extends TestCase
{
    /**
     * This test will make sure HTTP Repository "SuperHTTPRepo"
     * Communicates with SuperService without mocking the HTTP client
     * but by using a simulation in
     */
    public function testSuperAppDoesMagicWithSuperService()
    {
        //++ Arrange

        $hClient = new Client('http://myhoverflyhost:8888');

        // I am instantiating this Guzzle Client by changing its base_uri pointing
        // to Hoverfly's hostname and its webserver port.
        // Alternatively, you could use Hoverfly proxy to serve simulations
        $httpClient = new GuzzleHttpClient(['base_uri' => 'http://myhoverflyhost:8888']);

        // I pass this HTTP client to my HTTP Repo
        $superRepo = new SuperHTTPRepo($httpClient);

        // Make sure Hoverfly is set in simulation mode
        $hClient->setMode(Client::MODE_SIMULATION);

        // And set simulation in Hoverfly from within the test
        $hClient->setSimulation(Simulation::new()->withPair(
            Request::fromPSR7(
                new Psr7Request('GET', '/superapi/foo', ['Content-Type' => 'application/json'])
            ),
            Response::fromPSR7(
                new Psr7Response(200, ['Content-Type' => 'application/json'], '{"bar": "true"')
            )
        ));

        //++ Act

        // Now your repo can do its work by triggering a real HTTP request to simulated service
        $returnedValue = $superRepo->doMyAwesomeWork();

        //++ Assert

        // Finally, you can do your assertions
        $this->assertTrue($returnedValue);

        //++ Clean UP
        // Either here or in TearDown(), you might want to clear simulations with
        $hClient->deleteSimulation();
    }
}
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email pachicodev@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
