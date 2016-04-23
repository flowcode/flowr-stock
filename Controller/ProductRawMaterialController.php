<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\ProductRawMaterial;
use Flower\StockBundle\Form\Type\ProductRawMaterialType;
use Doctrine\ORM\QueryBuilder;

/**
 * Stock\ProductRawMaterial controller.
 *
 * @Route("/stock/productrawmaterial")
 */
class ProductRawMaterialController extends Controller
{
    /**
     * Lists all Stock\ProductRawMaterial entities.
     *
     * @Route("/", name="stock_productrawmaterial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\ProductRawMaterial')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'productrawmaterial');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Stock\ProductRawMaterial entity.
     *
     * @Route("/{id}/show", name="stock_productrawmaterial_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ProductRawMaterial $productrawmaterial)
    {
        $editForm = $this->createForm(new ProductRawMaterialType(), $productrawmaterial, array(
            'action' => $this->generateUrl('stock_productrawmaterial_update', array('id' => $productrawmaterial->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productrawmaterial->getId(), 'stock_productrawmaterial_delete');

        return array(

            'productrawmaterial' => $productrawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Stock\ProductRawMaterial entity.
     *
     * @Route("/new", name="stock_productrawmaterial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $productrawmaterial = new ProductRawMaterial();
        $form = $this->createForm(new ProductRawMaterialType(), $productrawmaterial);

        return array(
            'productrawmaterial' => $productrawmaterial,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Stock\ProductRawMaterial entity.
     *
     * @Route("/create", name="stock_productrawmaterial_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Stock\ProductRawMaterial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $productrawmaterial = new ProductRawMaterial();
        $form = $this->createForm(new ProductRawMaterialType(), $productrawmaterial);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productrawmaterial);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_productrawmaterial_show', array('id' => $productrawmaterial->getId())));
        }

        return array(
            'productrawmaterial' => $productrawmaterial,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Stock\ProductRawMaterial entity.
     *
     * @Route("/{id}/edit", name="stock_productrawmaterial_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Stock\ProductRawMaterial $productrawmaterial)
    {
        $editForm = $this->createForm(new ProductRawMaterialType(), $productrawmaterial, array(
            'action' => $this->generateUrl('stock_productrawmaterial_update', array('id' => $productrawmaterial->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productrawmaterial->getId(), 'stock_productrawmaterial_delete');

        return array(
            'productrawmaterial' => $productrawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Stock\ProductRawMaterial entity.
     *
     * @Route("/{id}/update", name="stock_productrawmaterial_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Stock\ProductRawMaterial:edit.html.twig")
     */
    public function updateAction(Stock\ProductRawMaterial $productrawmaterial, Request $request)
    {
        $editForm = $this->createForm(new ProductRawMaterialType(), $productrawmaterial, array(
            'action' => $this->generateUrl('stock_productrawmaterial_update', array('id' => $productrawmaterial->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_productrawmaterial_show', array('id' => $productrawmaterial->getId())));
        }
        $deleteForm = $this->createDeleteForm($productrawmaterial->getId(), 'stock_productrawmaterial_delete');

        return array(
            'productrawmaterial' => $productrawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_productrawmaterial_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('productrawmaterial', $field, $type);

        return $this->redirect($this->generateUrl('stock_productrawmaterial'));
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
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a Stock\ProductRawMaterial entity.
     *
     * @Route("/{id}/delete", name="stock_productrawmaterial_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Stock\ProductRawMaterial $productrawmaterial, Request $request)
    {
        $form = $this->createDeleteForm($productrawmaterial->getId(), 'stock_productrawmaterial_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productrawmaterial);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_productrawmaterial'));
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
