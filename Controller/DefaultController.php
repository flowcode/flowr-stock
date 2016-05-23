<?php

namespace Flower\StockBundle\Controller;

use Flower\ModelBundle\Entity\Stock\ProductRawMaterial;
use Flower\StockBundle\Entity\Setting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\Product;
use Flower\StockBundle\Form\Type\ProductType;
use Doctrine\ORM\QueryBuilder;

/**
 * Product controller.
 *
 * @Route("/stock")
 */
class DefaultController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="stock_dashboard")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $stats = array(
            'product_total_count' => $em->getRepository('FlowerModelBundle:Stock\Product')->getTotalCount(),
            'product_no_stock_count' => $em->getRepository('FlowerModelBundle:Stock\Product')->getTotalCountWithouhStock(),
            'rawmaterial_total_count' => 0,
            'rawmaterial_no_stock_count' => 0,
            'service_total_count' => $em->getRepository('FlowerModelBundle:Stock\Service')->getTotalCount(),
        );

        $stockStatus = $em->getRepository('FlowerStockBundle:Setting')->findBy(array(
            'name' => Setting::STOCK_VIEWABLE_SALE_STATUS,
        ));
        $viewableStatuses = array();
        foreach ($stockStatus as $status){
            $viewableStatuses[] = $status->getValue();
        }

        return array(
            'stats' => $stats,
            'pending_sales' => $em->getRepository('FlowerModelBundle:Sales\Sale')->getByStatus($viewableStatuses),
        );
    }


}
