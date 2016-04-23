<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\RawMaterial;
use Flower\StockBundle\Form\Type\RawMaterialType;
use Doctrine\ORM\QueryBuilder;

/**
 * Stock\RawMaterial controller.
 *
 * @Route("/stock/rawmaterial")
 */
class RawMaterialController extends Controller
{
    /**
     * Lists all Stock\RawMaterial entities.
     *
     * @Route("/", name="stock_rawmaterial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\RawMaterial')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'rawmaterial');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Stock\RawMaterial entity.
     *
     * @Route("/{id}/show", name="stock_rawmaterial_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(RawMaterial $rawmaterial)
    {
        $editForm = $this->createForm(new RawMaterialType(), $rawmaterial, array(
            'action' => $this->generateUrl('stock_rawmaterial_update', array('id' => $rawmaterial->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($rawmaterial->getId(), 'stock_rawmaterial_delete');

        return array(

            'rawmaterial' => $rawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Stock\RawMaterial entity.
     *
     * @Route("/new", name="stock_rawmaterial_new")
     * @Method("GET")
     * @Template()
     */
    public
    function newAction()
    {
        $rawmaterial = new RawMaterial();
        $form = $this->createForm(new RawMaterialType(), $rawmaterial);

        return array(
            'rawmaterial' => $rawmaterial,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Stock\RawMaterial entity.
     *
     * @Route("/create", name="stock_rawmaterial_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Stock\RawMaterial:new.html.twig")
     */
    public
    function createAction(Request $request)
    {
        $rawmaterial = new RawMaterial();
        $form = $this->createForm(new RawMaterialType(), $rawmaterial);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rawmaterial);
            $em->flush();

            return $this->redirect($this->generateUrl('stock_rawmaterial_show', array('id' => $rawmaterial->getId())));
        }

        return array(
            'rawmaterial' => $rawmaterial,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Stock\RawMaterial entity.
     *
     * @Route("/{id}/edit", name="stock_rawmaterial_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public
    function editAction(Stock\RawMaterial $rawmaterial)
    {
        $editForm = $this->createForm(new RawMaterialType(), $rawmaterial, array(
            'action' => $this->generateUrl('stock_rawmaterial_update', array('id' => $rawmaterial->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($rawmaterial->getId(), 'stock_rawmaterial_delete');

        return array(
            'rawmaterial' => $rawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Stock\RawMaterial entity.
     *
     * @Route("/{id}/update", name="stock_rawmaterial_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Stock\RawMaterial:edit.html.twig")
     */
    public function updateAction(Stock\RawMaterial $rawmaterial, Request $request)
    {
        $editForm = $this->createForm(new RawMaterialType(), $rawmaterial, array(
            'action' => $this->generateUrl('stock_rawmaterial_update', array('id' => $rawmaterial->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('stock_rawmaterial_show', array('id' => $rawmaterial->getId())));
        }
        $deleteForm = $this->createDeleteForm($rawmaterial->getId(), 'stock_rawmaterial_delete');

        return array(
            'rawmaterial' => $rawmaterial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="stock_rawmaterial_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('rawmaterial', $field, $type);

        return $this->redirect($this->generateUrl('stock_rawmaterial'));
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
     * Deletes a Stock\RawMaterial entity.
     *
     * @Route("/{id}/delete", name="stock_rawmaterial_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Stock\RawMaterial $rawmaterial, Request $request)
    {
        $form = $this->createDeleteForm($rawmaterial->getId(), 'stock_rawmaterial_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rawmaterial);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stock_rawmaterial'));
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
