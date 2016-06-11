<?php

namespace Flower\StockBundle\Controller;

use Flower\SalesBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Sales\Sale;
use Flower\SalesBundle\Form\Type\SaleType;
use Doctrine\ORM\QueryBuilder;
use Ps\PdfBundle\Annotation\Pdf;

/**
 * Sale controller.
 *
 * @Route("/stock/order")
 */
class OrderController extends BaseController
{
    /**
     * Lists all Sale entities.
     *
     * @Route("/", name="stock_order")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $saleAlias = "s";
        $accountAlias = "a";
        $qb = $em->getRepository('FlowerModelBundle:Sales\Sale')->createQueryBuilder($saleAlias);
        $qb->leftJoin("s.owner", "u");
        $qb->leftJoin("s.account", $accountAlias);
        $qb->leftJoin("s.status", "es");

        /* filter by org security groups */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $secGroupSrv = $this->get('user.service.securitygroup');
            $qb = $secGroupSrv->addLowerSecurityGroupsFilter($qb, $this->getUser(), $accountAlias);
        }

        $translator = $this->get('translator');
        $dateformat = $translator->trans('fullDateTime');
        $filters = array('accountFilter' => "a.id",
            'ownerFilter' => 's.owner',
            'statusFilter' => 'es.id',
            'startDateFilter' => array("field" => "s.created", "type" => "date", "format" => $dateformat, "operation" => ">"),
            'endDateFilter' => array("field" => "s.created", "type" => "date", "format" => $dateformat, "operation" => "<="));

        if ($request->query->has('reset')) {
            $request->getSession()->set('filter.sale', null);
            $request->getSession()->set('sort.sale', null);
            return $this->redirectToRoute("sale");
        }

        $this->saveFilters($request, $filters, 'sale', 'sale');
        $qb->andWhere("es.saleDeleted = 0 ");
        $paginator = $this->filter($qb, 'sale', $request);

        $accounts = $this->get("client.service.account")->findAll();
        $status = $em->getRepository('FlowerModelBundle:Sales\SaleStatus')->findBy(array("saleDeleted" => false));
        $users = $em->getRepository('FlowerModelBundle:User\User')->findAll();
        $accountFilter = $request->query->get("accountFilter");
        $filters = $this->getFilters('sale');
        if (!$accountFilter && $filters['accountFilter'] && $filters['accountFilter']["value"]) {
            $accountFilter = $filters['accountFilter']["value"];
        }
        $ownerFilter = $request->query->get("ownerFilter");
        if (!$ownerFilter && $filters['ownerFilter'] && $filters['ownerFilter']["value"]) {
            $ownerFilter = $filters['ownerFilter']["value"];
        }
        $statusFilter = $request->query->get("statusFilter");
        if (!$statusFilter && $filters['statusFilter'] && $filters['statusFilter']["value"]) {
            $statusFilter = $filters['statusFilter']["value"];
        }
        return array(
            'startDateFilter' => isset($filters['startDateFilter']) ? $filters['startDateFilter']["value"] : null,
            'endDateFilter' => isset($filters['endDateFilter']) ? $filters['endDateFilter']["value"] : null,
            'users' => $users,
            'statuses' => $status,
            'ownerFilter' => $ownerFilter,
            'statusFilter' => $statusFilter,
            'paginator' => $paginator,
            'accountFilter' => $accountFilter,
            'accounts' => $accounts,
        );
    }


    /**
     *
     * @Route("/export", name="stock_order_export")
     * @Method("GET")
     */
    public function exportViewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Sales\Sale')->createQueryBuilder('s');
        $qb->leftJoin("s.account", "a");

        $limit = 20;
        $currPage = $request->query->get('page');
        if ($currPage) {
            $sales = $this->filter($qb, 'sale', $request, $limit, $currPage);
        } else {
            $sales = $this->filter($qb, 'sale', $request, -1);
        }

        $data = $this->get("sales.service.sale")->saleDataExport($sales);
        $this->get("sales.service.excelexport")->exportData($data, "Ventas", "Exportación de ventas.");
        die();
        return $this->redirectToRoute("sale");
    }

    /**
     * Finds and displays a Sale entity.
     *
     * @Route("/{id}/show", name="stock_order_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template("FlowerStockBundle:Order:show.html.twig")
     */
    public function showAction(Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $paymentMethods = $em->getRepository('FlowerModelBundle:Sales\PaymentMethod')->findAll();
        return array(
            'sale' => $sale,
            'paymentMethods' => $paymentMethods,
        );
    }

    /**
     * Finds and displays a Sale entity.
     *
     * @Route("/{id}/prepare", name="stock_order_prepare", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template("FlowerStockBundle:Order:prepare.html.twig")
     */
    public function prepareAction(Request $request, Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $nextStatus = $request->get('status_to');

        $products = $em->getRepository('FlowerModelBundle:Stock\Product')->findBy(array("enabled" => true));

        return array(
            'sale' => $sale,
            'next_status' => $nextStatus,
            'products' => $products,
        );
    }

    /**
     * Finds and displays a Sale entity.
     *
     * @Route("/{id}/prepare_do", name="stock_order_prepare_do", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template("FlowerStockBundle:Order:prepare.html.twig")
     */
    public function prepareDoAction(Request $request, Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $items = $request->get('item');

        $stockService = $this->get('flower.stock.service.stock');
        $statusTo = $em->getRepository('FlowerModelBundle:Sales\SaleStatus')->find($request->get('status_to'));

        /* manage stock */
        foreach ($items as $itemId => $item) {

            /* prepare product */
            $saleItem = $em->getRepository('FlowerModelBundle:Sales\SaleItem')->find($itemId);

            $product = $saleItem->getProduct();
            $stockService->increaseProduct($product, $item['units'], false, "Preparación custom");

            //FIXME: Chequear que esta modificando stock.
            /* decrease custom raw materials */
            foreach ($item['rawMaterials'] as $rawMaterial) {
                $productRaw = $em->getRepository('FlowerModelBundle:Stock\Product')->find($rawMaterial['rawMaterial']);
                $stockService->decreaseProduct($productRaw, $item['units'] * $rawMaterial['quantity'], $sale);
            }

            /* descrease sale product */
            $stockService->decreaseProduct($product, $item['units'], $sale);

        }

        $sale->setStatus($statusTo);
        $em->flush();


        return $this->redirect($this->generateUrl('stock_order_show', array('id' => $sale->getId())));
    }

    /**
     * Finds and displays a Sale entity.
     *
     * @Route("/{id}/change_status", name="stock_order_change_status", requirements={"id"="\d+"})
     * @Method("POST")
     * @Template("FlowerStockBundle:Order:show.html.twig")
     */
    public function changeStatusAction(Sale $sale, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $statusTo = $em->getRepository('FlowerModelBundle:Sales\SaleStatus')->find($request->get('status_to'));

        if ($statusTo->isStockModifier()) {
            return $this->redirect($this->generateUrl('stock_order_prepare', array('id' => $sale->getId(), "status_to" => $request->get('status_to'))));
        }

        $sale->setStatus($statusTo);
        $em->flush();

        return $this->redirect($this->generateUrl('stock_order_show', array('id' => $sale->getId())));
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_order_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('sale', $field, $type);

        return $this->redirect($this->generateUrl('sale'));
    }

    /**
     * @Pdf()
     * @Route("/print/{id}/pdf", name="stock_order_pdf_export", requirements={"id"="\d+"},  defaults={ "_format"="pdf" })
     * @Template()
     */
    public function printPDFAction(Sale $sale)
    {
        $em = $this->getDoctrine()->getManager();
        $paymentMethods = $em->getRepository('FlowerModelBundle:Sales\PaymentMethod')->findAll();
        return array(
            'sale' => $sale,
            'paymentMethods' => $paymentMethods,
        );
    }
}
