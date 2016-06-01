<?php

namespace Flower\StockBundle\Controller;

use Flower\ModelBundle\Entity\Stock\ProductRawMaterial;
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
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $filter = array(
            'q' => $request->get('q'),
            'category' => $request->get('filter_category'),
            'is_rawmaterial' => $request->get('is_rawmaterial', false),
            'is_enabled' => $request->get('is_enabled', true),
        );
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\Product')->createQueryBuilder('p');
        $qb->join('p.category', 'c');


        if ($filter['q']) {
            $qb->andWhere('p.name LIKE :name')->setParameter('name', '%' . $filter['q'] . '%');
        }

        if ($filter['category']) {
            $qb->andWhere('c.id =:category_id')->setParameter('category_id', $filter['category']);
        }

        if ($filter['is_rawmaterial']) {
            $qb->andWhere('p.rawMaterial = :is_rawmaterial')->setParameter('is_rawmaterial', true);
        }

        if ($filter['is_enabled']) {
            $qb->andWhere('p.enabled = :is_enabled')->setParameter('is_enabled', true);
        }

        $availableCategories = $em->getRepository('FlowerModelBundle:Stock\ProductCategory')->findBy(array());

        $this->addQueryBuilderSort($qb, 'product');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->get('page', 1), 20);

        return array(
            'availableCategories' => $availableCategories,
            'filter' => $filter,
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/stock_increase", name="product_stock_increase", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function stockIncreaseAction(Product $product)
    {

        return array(
            'product' => $product,
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/stock_increase", name="product_stock_do_increase", requirements={"id"="\d+"})
     * @Method("POST")
     * @Template()
     */
    public function stockIncreaseDoAction(Request $request, Product $product)
    {

        if ($request->get('quantity')) {

            $stockService = $this->get('flower.stock.service.stock');

            $stockService->increaseProduct($product, $request->get('quantity'), true, $request->get('comments'));

            return $this->redirect($this->generateUrl('product_show', array('id' => $product->getId())));
        }

        return $this->redirect($this->generateUrl('product_stock_increase', array('id' => $product->getId())));
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/stock_decrease", name="product_stock_decrease", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function stockDecreaseAction(Product $product)
    {
        return array(
            'product' => $product,
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/stock_decrease", name="product_stock_do_decrease", requirements={"id"="\d+"})
     * @Method("POST")
     * @Template()
     */
    public function stockDecreaseDoAction(Request $request, Product $product)
    {

        if ($request->get('quantity')) {

            $stockService = $this->get('flower.stock.service.stock');

            $stockService->decreaseProduct($product, $request->get('quantity'), null, $request->get('comments'));

            return $this->redirect($this->generateUrl('product_show', array('id' => $product->getId())));
        }

        return $this->redirect($this->generateUrl('product_stock_increase', array('id' => $product->getId())));
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/show", name="product_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Product $product)
    {
        $editForm = $this->createForm(new ProductType(), $product, array(
            'action' => $this->generateUrl('product_update', array('id' => $product->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($product->getId(), 'product_delete');

        $em = $this->getDoctrine()->getManager();

        $changeLogs = $em->getRepository('FlowerStockBundle:StockChangeLog')->findBy(array("product" => $product), array("date" => "DESC"), 10);

        return array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'changeLogs' => $changeLogs,
        );
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="product_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $product = new Product();

        $form = $this->createForm(new ProductType(), $product);

        return array(
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/create", name="product_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Product:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($product->getRawMaterials() as $productRawMaterial) {
                $productRawMaterial->setProduct($product);
            }

            $product = $this->get('flower.stock.service.stock')->uploadImage($product);

            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('product_show', array('id' => $product->getId())));
        }

        return array(
            'product' => $product,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="product_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Product $product)
    {
        $editForm = $this->createForm(new ProductType(), $product, array(
            'action' => $this->generateUrl('product_update', array('id' => $product->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($product->getId(), 'product_delete');

        return array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}/update", name="product_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:Product:edit.html.twig")
     */
    public function updateAction(Product $product, Request $request)
    {
        $editForm = $this->createForm(new ProductType(), $product, array(
            'action' => $this->generateUrl('product_update', array('id' => $product->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {

            foreach ($product->getRawMaterials() as $productRawMaterial) {
                $productRawMaterial->setProduct($product);
            }

            $product = $this->get('flower.stock.service.stock')->uploadImage($product);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('product_show', array('id' => $product->getId())));
        }
        $deleteForm = $this->createDeleteForm($product->getId(), 'product_delete');

        return array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="product_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('product', $field, $type);

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            if (strpos($order['field'], '.') !== FALSE){
                $qb->orderBy($order['field'], $order['type']);
            }else{
                $qb->orderBy($alias . '.' . $order['field'], $order['type']);
            }
        }
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}/delete", name="product_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Product $product, Request $request)
    {
        $form = $this->createDeleteForm($product->getId(), 'product_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}
