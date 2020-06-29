<?php declare (strict_types=1);

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ClassWithArrayAndManyResponseController extends AbstractController
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        return $this->handleStuff();
    }

    /**
     * @return string[]|Response
     */
    private function handleStuff()
    {
        if (mt_rand(0, 100)) {
            return [
                'hello' => 'world'
            ];
        } else {
            return new JsonResponse([]);
        }

        return new RedirectResponse('elsewhere');
    }
}

?>
-----
<?php declare (strict_types=1);

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ClassWithArrayAndManyResponseController extends AbstractController
{
    public function indexAction(): \Symfony\Component\HttpFoundation\Response
    {
        $response = $this->handleStuff();
        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }
        return $this->render('@App/class_with_array_and_many_response/index.html.twig');
    }

    /**
     * @return string[]|Response
     */
    private function handleStuff()
    {
        if (mt_rand(0, 100)) {
            return [
                'hello' => 'world'
            ];
        } else {
            return new JsonResponse([]);
        }

        return new RedirectResponse('elsewhere');
    }
}

?>