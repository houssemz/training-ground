<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\Shared\TestCase;

use App\Tests\PHPUnit\Shared\DependencyInjection\ContainerAccessBehaviour;
use App\Tests\PHPUnit\Shared\Persistence\TransactionalBehavior;
use Helmich\JsonAssert\Constraint\JsonValueMatchesMany;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiTestCase extends WebTestCase
{
    use ContainerAccessBehaviour;
    use JsonAssertions;
    use TransactionalBehavior;

    protected static function createAuthenticatedClient(string|UserInterface $user): AbstractBrowser
    {
        $client = self::client(parent::createClient());

        if (\is_string($user)) {
            $user = self::get(UserProviderInterface::class)->loadUserByIdentifier($user);
        }

        return $client->loginUser($user);
    }

    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        return self::client(parent::createClient($options, $server));
    }

    protected static function symfonyRequest(): Request
    {
        return self::client()->getRequest();
    }

    protected static function symfonyResponse(): Response
    {
        return self::client()->getResponse();
    }

    protected function thenTheValueAtJsonPathShouldBe(string $path, $expected): void
    {
        $content = $this->symfonyResponse()->getContent();

        self::assertNotFalse($content);
        self::assertJson($content);
        static::assertJsonValueEquals($content, $path, $expected);
    }

    /** @param array<string, mixed> $expected */
    protected function thenEachValueAtJsonPathShouldRespectivelyBe(array $expected): void
    {
        $content = $this->symfonyResponse()->getContent();

        self::assertNotFalse($content);
        self::assertJson($content);
        self::assertThat($content, new JsonValueMatchesMany($expected));
    }

    private static function client(?KernelBrowser $newClient = null): KernelBrowser
    {
        static $client;

        if (\func_num_args() > 0) {
            return $client = $newClient;
        }

        if (!$client instanceof AbstractBrowser) {
            self::fail(sprintf('A client must be set to make assertions on it. Did you forget to call "%s::createClient()"?', __CLASS__));
        }

        return $client;
    }
}
