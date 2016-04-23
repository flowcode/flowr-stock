<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\RawMaterialCategory;
use Flower\StockBundle\Form\Type\RawMaterialCategoryType;
use Doctrine\ORM\QueryBuilder;

/**
 * Stock\RawMaterialCategory controller.
 *
 * @Route("/stock_rawmaterialcategory")
 */
class RawMaterialCategoryController extends Controller
{
    /**
     * Lists all Stock\RawMaterialCategory entities.
     *
     * @Route("/", name="stock_rawmaterialcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\RawMaterialCategory')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'rawmaterialcategory');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Stock\RawMaterialCategory entity.
     *
     * @Route("/{id}/show", name="stock_rawmaterialcategory_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(RawMaterialCategory $rawmaterialcategory)
    {
        $editForm = $this->createForm(new RawMaterialCategoryType(), $rawmaterialcategory, array(
            'action' => $this->generateUrl('stock_rawmaterialcategory_update', array('id' => $rawmaterialcategory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($rawmaterialcategory->getId(), 'stock_rawmaterialcategory_delete');

        return array(

            'rawmaterialcategory' => $rawmaterialcategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Stock\RawMaterialCategory entity.
     *
     * @Route("/new", name="stock_rawmaterialcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $rawmaterialcategory = new RawMaterialCategory();
        $form = $this->createForm(new RawMaterialCategoryType(), $rawmaterialcategory);

        return array(
            'rawmaterialcategory' => $rawmaterialcategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Stock\RawMaterialCategory entity.
     *
     * @Route("/create", name="stock_rawmaterialcategory_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Stock\RawMaterialCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $rawmaterialcategory = new RawMaterialCategory();
        $form = $this->createForm(new RawMaterialCategoryType(), $rawmaterialcategory);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rawmaterialcategory);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_rawmaterialcategory_show', array('id' => $rawmaterialcategory->getId())));
        }

        return array(
            'rawmaterialcategory' => $rawmaterialcategory,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Stock\RawMaterialCategory entity.
     *
     * @Route("/{id}/edit", name="stock_rawmaterialcategory_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Stock\RawMaterialCategory $rawmaterialcategory)
    {
        $editForm = $this->createForm(new RawMaterialCategoryType(), $rawmaterialcategory, array(
            'action' => $this->generateUrl('stock_rawmaterialcategory_update', array('id' => $rawmaterialcategory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($rawmaterialcategory->getId(), 'stock_rawmaterialcategory_delete');

        return array(
            'rawmaterialcategory' => $rawmaterialcategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Stock\RawMaterialCategory entity.
     *
     * @Route("/{id}/update", name="stock_rawmaterialcategory_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Stock\RawMaterialCategory:edit.html.twig")
     */
    public function updateAction(Stock\RawMaterialCategory $rawmaterialcategory, Request $request)
    {
        $editForm = $this->createForm(new RawMaterialCategoryType(), $rawmaterialcategory, array(
            'action' => $this->generateUrl('stock_rawmaterialcategory_update', array('id' => $rawmaterialcategory->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_rawmaterialcategory_show', array('id' => $rawmaterialcategory->getId())));
        }
        $deleteForm = $this->createDeleteForm($rawmaterialcategory->getId(), 'stock_rawmaterialcategory_delete');

        return array(
            'rawmaterialcategory' => $rawmaterialcategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_rawmaterialcategory_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('rawmaterialcategory', $field, $type);

        return $this->redirect($this->generateUrl('stock_rawmaterialcategory'));
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
     * Deletes a Stock\RawMaterialCategory entity.
     *
     * @Route("/{id}/delete", name="stock_rawmaterialcategory_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Stock\RawMaterialCategory $rawmaterialcategory, Request $request)
    {
        $form = $this->createDeleteForm($rawmaterialcategory->getId(), 'stock_rawmaterialcategory_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rawmaterialcategory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_rawmaterialcategory'));
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
