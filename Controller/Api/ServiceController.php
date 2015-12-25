<?php

namespace Flower\StockBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service controller.
 */
class ServiceController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('FlowerModelBundle:Stock\Service')->findBy(array("enabled" => true));

        $view = FOSView::create($products, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

}