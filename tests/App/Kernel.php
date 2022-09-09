<?php declare(strict_types=1);

namespace Sofyco\Bundle\DemoBundle\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        yield new \Sofyco\Bundle\DemoBundle\DemoBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['test' => true]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('index', '/')->controller(__CLASS__);
    }

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse($request->getContent(), Response::HTTP_OK, [], true);
    }
}
